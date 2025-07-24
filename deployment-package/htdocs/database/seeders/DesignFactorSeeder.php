<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DesignFactor;

class DesignFactorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designFactors = [
            [
                'code' => 'DF1',
                'title' => 'Enterprise Strategy',
                'description' => 'Stratégie d\'entreprise - Orientation stratégique et approche technologique',
                'labels' => ['Croissance', 'Stabilité', 'Coût', 'Innovation'],
                'defaults' => [3, 3, 3, 3],
                'order' => 1,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 1,
                    'max' => 5,
                    'step' => 1
                ]
            ],
            [
                'code' => 'DF2',
                'title' => 'Enterprise Goals',
                'description' => 'Objectifs d\'entreprise - Priorités et objectifs métier',
                'labels' => ['Portefeuille agile', 'Risques métier', 'Conformité réglementaire', 'Objectif 4'],
                'defaults' => [1, 1, 1, 1],
                'order' => 2,
                'metadata' => [
                    'type' => 'dropdown',
                    'options' => [
                        ['value' => 1, 'label' => 'Très faible'],
                        ['value' => 2, 'label' => 'Faible'],
                        ['value' => 3, 'label' => 'Moyen'],
                        ['value' => 4, 'label' => 'Élevé']
                    ]
                ]
            ],
            [
                'code' => 'DF3',
                'title' => 'Enterprise Risk Profile',
                'description' => 'Profil de risque - Tolérance au risque et appétit pour le risque',
                'labels' => ['Investissement IT', 'Gestion programmes', 'Coûts IT', 'Expertise IT'],
                'defaults' => [3, 3, 3, 3],
                'order' => 3,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 1,
                    'max' => 5,
                    'step' => 1
                ]
            ],
            [
                'code' => 'DF4',
                'title' => 'IT-Related Issues',
                'description' => 'Problèmes IT - Défis et problèmes liés aux technologies',
                'labels' => ['Problème IT 1', 'Problème IT 2', 'Problème IT 3', 'Problème IT 4'],
                'defaults' => [3, 3, 3, 3],
                'order' => 4,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 1,
                    'max' => 5,
                    'step' => 1
                ]
            ],
            [
                'code' => 'DF5',
                'title' => 'Threat Landscape',
                'description' => 'Paysage des menaces - Menaces externes et internes',
                'labels' => ['Menaces externes', 'Menaces internes'],
                'defaults' => [0.5, 0.5],
                'order' => 5,
                'metadata' => [
                    'type' => 'checkbox',
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.1
                ]
            ],
            [
                'code' => 'DF6',
                'title' => 'Compliance Requirements',
                'description' => 'Exigences de conformité - Réglementations et standards',
                'labels' => ['Exigences réglementaires', 'Exigences sectorielles', 'Exigences internes'],
                'defaults' => [0.5, 0.5, 0.5],
                'order' => 6,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.1
                ]
            ],
            [
                'code' => 'DF7',
                'title' => 'Role of IT',
                'description' => 'Rôle de l\'IT - Position et fonction de l\'IT dans l\'organisation',
                'labels' => ['Support', 'Factory', 'Turnaround'],
                'defaults' => [3, 3, 3],
                'order' => 7,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 1,
                    'max' => 4,
                    'step' => 1
                ]
            ],
            [
                'code' => 'DF8',
                'title' => 'Sourcing Model',
                'description' => 'Modèle d\'approvisionnement - Stratégie de sourcing IT',
                'labels' => ['Modèle interne', 'Modèle externe'],
                'defaults' => [1, 1],
                'order' => 8,
                'metadata' => [
                    'type' => 'dropdown',
                    'options' => [
                        ['value' => 1, 'label' => 'Interne uniquement'],
                        ['value' => 2, 'label' => 'Mixte'],
                        ['value' => 3, 'label' => 'Externe principalement']
                    ]
                ]
            ],
            [
                'code' => 'DF9',
                'title' => 'Implementation Methods',
                'description' => 'Méthodes d\'implémentation - Approches de développement et déploiement',
                'labels' => ['Méthodes agiles', 'DevOps', 'Méthodes traditionnelles'],
                'defaults' => [0.5, 0.5, 0.5],
                'order' => 9,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.1
                ]
            ],
            [
                'code' => 'DF10',
                'title' => 'Enterprise Size',
                'description' => 'Taille de l\'entreprise - Dimension et complexité organisationnelle',
                'labels' => ['Petite entreprise', 'Moyenne entreprise', 'Grande entreprise'],
                'defaults' => [0.5, 0.5, 0.5],
                'order' => 10,
                'metadata' => [
                    'type' => 'slider',
                    'min' => 0,
                    'max' => 1,
                    'step' => 0.1
                ]
            ]
        ];

        foreach ($designFactors as $df) {
            DesignFactor::updateOrCreate(
                ['code' => $df['code']],
                array_merge($df, [
                    'is_active' => true,
                    'metadata' => [
                        'created_by_seeder' => true,
                        'version' => '1.0'
                    ]
                ])
            );
        }
    }
}
