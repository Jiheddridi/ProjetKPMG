const { spawn } = require('child_process');
const path = require('path');

exports.handler = async (event, context) => {
  return new Promise((resolve, reject) => {
    const php = spawn('php', [
      path.join(__dirname, '../../public/index.php')
    ], {
      env: {
        ...process.env,
        REQUEST_METHOD: event.httpMethod,
        REQUEST_URI: event.path,
        QUERY_STRING: event.rawQuery || '',
        HTTP_HOST: event.headers.host,
        HTTP_USER_AGENT: event.headers['user-agent'] || '',
        CONTENT_TYPE: event.headers['content-type'] || '',
        CONTENT_LENGTH: event.body ? Buffer.byteLength(event.body) : '0',
        HTTPS: 'on',
        SERVER_NAME: event.headers.host,
        SERVER_PORT: '443',
        SCRIPT_NAME: '/index.php',
        SCRIPT_FILENAME: path.join(__dirname, '../../public/index.php'),
        DOCUMENT_ROOT: path.join(__dirname, '../../public')
      }
    });

    let output = '';
    let error = '';

    php.stdout.on('data', (data) => {
      output += data.toString();
    });

    php.stderr.on('data', (data) => {
      error += data.toString();
    });

    if (event.body) {
      php.stdin.write(event.body);
    }
    php.stdin.end();

    php.on('close', (code) => {
      if (code !== 0) {
        console.error('PHP Error:', error);
        resolve({
          statusCode: 500,
          body: 'Internal Server Error'
        });
        return;
      }

      const [headers, body] = output.split('\r\n\r\n', 2);
      const headerLines = headers.split('\r\n');
      const responseHeaders = {};
      let statusCode = 200;

      headerLines.forEach(line => {
        if (line.startsWith('Status: ')) {
          statusCode = parseInt(line.split(' ')[1]);
        } else if (line.includes(': ')) {
          const [key, value] = line.split(': ', 2);
          responseHeaders[key] = value;
        }
      });

      resolve({
        statusCode,
        headers: responseHeaders,
        body: body || output
      });
    });
  });
};
