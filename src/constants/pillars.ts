const pillars = [
  {
    icon: '🌍',
    model: "assets/models/earth-cartoon.glb",
    title: 'Inclusif',
    camera: [0.0, 0.0, 4.0],
    ambientLightIntensity: 0.6,
    color: 'text-blue-400',
    description: 'Un numérique accessible à tous, sans fracture',
    actions: [
      'Reconditionnement de machines',
      'Distribution d\'ordinateurs sous Linux',
      'Formation des élèves non-équipés',
      'Solidarité interne au lycée'
    ]
  },
  {
    icon: '💡',
    model: "assets/models/eco-light.glb",
    title: 'Responsable',
    camera: [0.0, 0.0, 6.0],
    ambientLightIntensity: 3,
    color: 'text-green-400',
    description: 'Émancipation et souveraineté numérique',
    actions: [
      'Adoption de logiciels libres',
      'Compréhension des outils',
      'Choix technologiques éclairés',
      'Formation des équipes'
    ]
  },
  {
    icon: '⌛',
    model: "assets/models/hourglass.glb",
    title: 'Durable',
    camera: [0.0, 0.0, 8.0],
    ambientLightIntensity: 3,
    color: 'text-purple-400',
    description: 'Réduction de l\'empreinte écologique',
    actions: [
      'Allongement durée de vie matériel',
      'Hébergement local des données',
      'Optimisation énergétique',
      'Réduction des déchets électroniques'
    ]
  }
]

export default pillars;
