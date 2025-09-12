<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class UsernameGenerator {
    
    // Word lists for different themes
    private $themes = [
        'Fantasy' => [
            'adjectives' => ['Epic', 'Evil', 'Legendary', 'Shadow', 'Dark', 'Fire', 'Ice', 'Storm', 'Lightning', 'Mystic', 'Cyber', 'Neon', 'Plasma', 'Toxic', 'Savage', 'Elite', 'Pro', 'Mega', 'Ultra', 'Super', 'Alpha', 'Beta', 'Omega', 'Prime', 'Stealth', 'Phantom', 'Ghost', 'Burned', 'Ordinary', 'Infernal', 'Sacred', 'Divine', 'Holy', 'Unholy', 'Cursed', 'Blessed', 'Arcane', 'Ancient', 'Eternal', 'Immortal', 'Mortal', 'Mighty', 'Brave', 'Bold', 'Wild', 'Feral', 'Hidden', 'Silent', 'Lunar', 'Solar', 'Stellar', 'Cosmic', 'Astral', 'Celestial', 'Inferno', 'Frozen', 'Icy', 'Fiery', 'Stormy', 'Thunderous', 'Electric', 'Venomous', 'Deadly', 'Fatal', 'Vicious', 'Cruel', 'Brutal', 'Grim', 'Dire', 'Dread', 'Fearsome', 'Wicked', 'Malevolent', 'Dead', 'Living', 'Undead', 'Ghastly', 'Gloomy', 'Glorious', 'Heroic', 'Noble', 'Royal', 'Regal', 'Valiant', 'Gallant', 'Honored', 'Renowned', 'Famous', 'Forgotten', 'Lost', 'Secret', 'Lurking', 'Stalking', 'Swift', 'Quick', 'Rapid', 'Speedy'],
            'nouns' => ['Warrior', 'Hunter', 'Assassin', 'Mage', 'Ninja', 'Samurai', 'Knight', 'Wizard', 'Ranger', 'Sniper', 'Fighter', 'Striker', 'Destroyer', 'Guardian', 'Champion', 'Hero', 'Legend', 'Master', 'Lord', 'King', 'Emperor', 'Slayer', 'Reaper', 'Beast', 'Archer', 'Ghost', 'Shadow', 'Blade', 'Arrow', 'Bolt', 'Fury', 'Rage', 'Storm', 'Flame', 'Frost', 'Ice', 'Thunder', 'Lightning', 'Venom', 'Toxic', 'Devil', 'God', 'Titan', 'Giant', 'Colossus', 'Juggernaut', 'Berserker', 'Paladin', 'Druid', 'Shaman', 'Monk', 'Pirate', 'Viking', 'Ronin', 'Spy', 'Agent', 'Soldier', 'Marine', 'Captain', 'Commander', 'General', 'Unicorn', 'Pegasus', 'Griffin', 'Hydra', 'Cerberus', 'Chimera', 'Minotaur', 'Cyclops', 'Sphinx', 'Golem', 'Elemental', 'Wraith', 'Banshee', 'Lich', 'Necromancer', 'Sorcerer', 'Enchanter', 'Alchemist', 'Seer', 'Oracle', 'Prophet', 'Sage', 'Mystic', 'Visionary', 'Dreamer', 'Wanderer', 'Nomad', 'Rogue', 'Thief', 'Bandit', 'Outlaw', 'Mercenary', 'Gladiator', 'Duelist', 'Swashbuckler', 'Corsair', 'Buccaneer', 'Privateer', 'Adventurer', 'Explorer', 'Pioneer', 'Pathfinder', 'Voyager', 'Seeker', 'Chaser', 'Runner', 'Sprinter', 'Jumper', 'Leaper', 'Climber', 'Diver', 'Swimmer', 'Sailor', 'Navigator', 'Pilot', 'Driver', 'Rider', 'Jockey', 'Demon', 'Angel', 'Dragon', 'Phoenix', 'Valkyrie', 'Warlock', 'Inquisitor', 'Templar', 'Crusader', 'Summoner', 'Conjurer', 'Illusionist', 'Trickster', 'Harbinger', 'Herald', 'Revenant', 'Phantom', 'Skeleton', 'Zombie', 'Ghoul', 'Goblin', 'Orc', 'Troll', 'Ogre', 'Kobold', 'Hobgoblin', 'Elf', 'Fairy', 'Pixie', 'Dryad', 'Nymph', 'Satyr', 'Centaur', 'Werewolf', 'Lycan', 'Vampire', 'Harpy', 'Kraken', 'Leviathan', 'Behemoth', 'Basilisk', 'Manticore', 'Ifrit', 'Djinn', 'Efreet', 'Genie', 'Imp', 'Sprite', 'Sylph', 'Naga', 'Seraph', 'Cherub', 'Archon', 'Angelus', 'Lamia', 'Rakshasa']
        ],
        'Professional' => [
            'adjectives' => ['General', 'Smart', 'Wise', 'Bright', 'Sharp', 'Quick', 'Swift', 'Clever', 'Creative', 'Innovative', 'Dynamic', 'Strategic', 'Efficient', 'Productive', 'Reliable', 'Focused', 'Skilled', 'Expert', 'Advanced', 'Premium', 'Quality', 'Modern', 'Fresh', 'Clean', 'Clear', 'Solid', 'Strong', 'Stable', 'Secure', 'Professional', 'Corporate', 'Organized', 'Dedicated', 'Committed', 'Motivated', 'Ambitious', 'Passionate', 'Driven', 'Hardworking', 'Diligent', 'Thorough', 'Precise', 'Accurate', 'Consistent', 'Dependable', 'Responsible', 'Accountable', 'Trustworthy', 'Honest', 'Ethical', 'Fair', 'Respectful', 'Confident', 'Capable', 'Talented', 'Gifted', 'Visionary', 'Resourceful', 'Flexible', 'Adaptable', 'Versatile', 'Collaborative', 'Cooperative', 'Supportive', 'Helpful', 'Positive', 'Proactive', 'Energetic', 'Enthusiastic', 'Charismatic', 'Influential', 'Persuasive', 'Analytical', 'Logical', 'Critical', 'Tactical', 'Practical', 'Realistic', 'Balanced', 'Trusted', 'Respected', 'Valued', 'Unique', 'Elite', 'Notable', 'Famous', 'Renowned', 'Distinguished', 'Recognized', 'Clearheaded', 'Sharpest', 'Brightest', 'Decisive', 'Innovative', 'Creative', 'Astute', 'Noble', 'Esteemed', 'Dependable', 'Capable'],
            'nouns' => ['Dev', 'Coder', 'Tech', 'Logic', 'Code', 'Data', 'Mind', 'Brain', 'Think', 'Idea', 'Solution', 'Builder', 'Maker', 'Creator', 'Designer', 'Engineer', 'Analyst', 'Consultant', 'Expert', 'Specialist', 'Manager', 'Leader', 'Director', 'Chief', 'Pro', 'Ace', 'Star', 'Elite', 'Prime', 'Core', 'Meeting', 'Report', 'Project', 'Client', 'Budget', 'Invoice', 'Contract', 'Strategy', 'Plan', 'Goal', 'Vision', 'Mission', 'Value', 'Growth', 'Success', 'Management', 'Team', 'Partner', 'Resource', 'Service', 'Support', 'Development', 'Innovation', 'Performance', 'Efficiency', 'Quality', 'Improvement', 'Leadership', 'Collaboration', 'Communication', 'Negotiation', 'Presentation', 'Analysis', 'Research', 'Marketing', 'Sales', 'Finance', 'Accounting', 'Operations', 'Business', 'Enterprise', 'Industry', 'Market', 'Customer', 'Vendor', 'Supplier', 'Product', 'Brand', 'Reputation', 'Network', 'Connection', 'Opportunity', 'Challenge', 'Risk', 'Compliance', 'Regulation', 'Policy', 'Procedure', 'Process', 'Terms', 'Conditions', 'Agreement', 'Partnership', 'Alliance', 'Merger', 'Acquisition', 'Investment', 'Funding', 'Capital', 'Equity', 'Asset', 'Liability', 'Revenue', 'Profit', 'Margin', 'Cost', 'Expense', 'Forecast', 'Trend', 'Insight', 'Metric', 'KPI', 'Board', 'Chairman', 'Executive', 'Officer', 'Employee', 'Staff', 'Worker', 'Associate', 'Intern', 'Trainee', 'Mentor', 'Coach', 'Advisor', 'Directorate', 'Committee', 'Division', 'Department', 'Unit', 'Branch', 'Subsidiary', 'Franchise', 'Startup', 'Company', 'Corporation', 'Firm', 'Agency', 'Organization', 'Institution', 'Foundation', 'Association', 'Group', 'Club', 'Union', 'Council', 'Authority', 'Regulator', 'Shareholder', 'Stakeholder', 'Investor', 'Entrepreneur', 'Founder', 'Owner', 'Partner', 'Share', 'Stock', 'Bond', 'Security', 'Exchange', 'Broker', 'Trader', 'Analyst', 'Planner', 'Strategist', 'Auditor', 'Controller', 'Treasurer', 'Cashflow', 'Dividend', 'Portfolio', 'Valuation', 'Assessment', 'Benchmark', 'Standard', 'Score', 'Rating', 'Index', 'Report', 'Summary', 'Document', 'File', 'Record', 'Archive', 'Note', 'Memo', 'Agenda', 'Schedule', 'Timeline', 'Deadline', 'Milestone', 'Task', 'Assignment', 'Duty', 'Role', 'Position', 'Title', 'Job', 'Career', 'Profession', 'Occupation', 'Workplace', 'Office', 'HQ', 'Branch', 'Outlet', 'Store', 'Shop', 'Outlet', 'Department', 'Floor', 'Room', 'Lobby', 'Reception']
        ],
        'Science and Space' => [
            'adjectives' => ['Stellar', 'Cosmic', 'Astronomical', 'Galactic', 'Solar', 'Lunar', 'Orbital', 'Nuclear', 'Quantum', 'Atomic', 'Molecular', 'Celestial', 'Interstellar', 'Planetary', 'Astral', 'Nebular', 'Binary', 'Gravitational', 'Magnetic', 'Electric', 'Thermal', 'Radiant', 'Photonic', 'Neutronic', 'Ionic', 'Plasma', 'Fusion', 'Fission', 'Relativistic', 'Dimensional', 'Temporal', 'Nano', 'Tetra', 'Octa', 'Hexa', 'Penta', 'Tri', 'Duo', 'Mono', 'Hyper', 'Ultra', 'Mega', 'Giga', 'Tera', 'Pico', 'Femto', 'Atto', 'Micro', 'Milli', 'Centi', 'Deci', 'Kilo', 'Hecto', 'Deca', 'Exo', 'Zetta', 'Yotta', 'Extra', 'Intra', 'Inter', 'Universal', 'Infinite', 'Expanding', 'Contracting', 'Curved', 'Warped', 'Bent', 'Singular', 'Eventful', 'Horizonless', 'Void', 'Vacuum', 'Dark', 'Luminous', 'Iridescent', 'Shimmering', 'Blazing', 'Fiery', 'Icy', 'Frozen', 'Glacial', 'Volcanic', 'Seismic', 'Asteroidal', 'Cometary', 'Meteoric', 'Eclipsing', 'Rotational', 'Spinning', 'Accelerated', 'Decelerated', 'Chaotic', 'Ordered', 'Harmonic', 'Resonant', 'Elastic', 'Inelastic', 'Fractal', 'Multiversal', 'Extragalactic', 'Hyperdimensional', 'Microscopic', 'Macroscopic'],
            'nouns' => ['Star', 'Planet', 'Moon', 'Galaxy', 'Nebula', 'Comet', 'Asteroid', 'Meteor', 'Satellite', 'Orbit', 'Cosmos', 'Universe', 'Quasar', 'Pulsar', 'Blackhole', 'Supernova', 'Wormhole', 'Spaceship', 'Rocket', 'Probe', 'Station', 'Laboratory', 'Observatory', 'Telescope', 'Microscope', 'Experiment', 'Theory', 'Formula', 'Equation', 'Hypothesis', 'Research', 'Science', 'Physics', 'Chemistry', 'Biology', 'Astronomy', 'Cosmology', 'Astrophysics', 'Quantum', 'Atom', 'Molecule', 'Electron', 'Proton', 'Neutron', 'Photon', 'Particle', 'Wave', 'Energy', 'Matter', 'Force', 'Field', 'Dimension', 'Time', 'Space', 'Void', 'Infinity', 'Voyager', 'Explorer', 'Pioneer', 'Endeavour', 'Apollo', 'Artemis', 'Hubble', 'Kepler', 'Galileo', 'Newton', 'Einstein', 'Hawking', 'Tesla', 'Curie', 'Cassini', 'Opportunity', 'Metric', 'Vector', 'Scalar', 'Tensor', 'Relativity', 'String', 'Brane', 'Multiverse', 'Singularity', 'Event', 'Horizon', 'Lightyear', 'Parsec', 'Astronaut', 'Cosmonaut', 'Mission', 'Flight', 'Launch', 'Docking', 'EVA', 'Gravity', 'Magnetism', 'Radiation', 'Spectrum', 'Wavelength', 'Frequency', 'Amplitude', 'Velocity', 'Acceleration', 'Momentum', 'Inertia', 'Mass', 'Charge', 'Spin', 'Trajectory', 'Escape', 'Surface', 'Core', 'Crust', 'Mantle', 'Atmosphere', 'Climate', 'Weather', 'Ecosystem', 'Biosphere', 'Eclipse', 'Cluster', 'Supercluster', 'Exoplanet', 'Exomoon', 'Redshift', 'Blueshift', 'BigBang', 'Expansion', 'Contraction', 'Annihilation', 'Creation', 'Plasma', 'RadiationBelt', 'Aurora', 'Sunspot', 'Solarflare', 'Magnetosphere', 'AsteroidBelt', 'KuiperBelt', 'OortCloud', 'Exosphere', 'Stratosphere', 'Troposphere', 'Ionosphere', 'Chronosphere', 'Laser', 'Spectrometer', 'TelescopeArray', 'SatelliteDish', 'Detector', 'Accelerator', 'Collider', 'Hadron', 'Lepton', 'Boson', 'Quark', 'Gluon', 'Neutrino', 'Muon', 'Tau', 'Antimatter', 'DarkMatter', 'DarkEnergy', 'SingularityPoint', 'Spacetime', 'Worldline', 'Lightcone', 'Manifold', 'Curvature', 'RedGiant', 'WhiteDwarf', 'BrownDwarf', 'PulsatingStar', 'BinarySystem', 'GlobularCluster', 'OpenCluster', 'GalaxyCluster', 'SuperEarth', 'HotJupiter', 'RoguePlanet', 'Exobiology', 'Terraforming', 'Cryogenics', 'Nanotechnology', 'Astrobiology', 'Astrochemistry', 'Geophysics', 'Volcano', 'TectonicPlate', 'Glacier', 'PolarCap', 'Meteorite', 'ImpactCrater', 'SpaceDebris', 'Payload', 'Module', 'Capsule', 'DockingPort', 'Launcher', 'SpaceSuit', 'Robotics', 'AI', 'Sensor', 'ProbeArm']
        ],
        'Computer Technology' => [
            'adjectives' => ['Digital', 'Cyber', 'Virtual', 'Smart', 'Intelligent', 'Automated', 'Connected', 'Wireless', 'Wired', 'Mobile', 'Cloud', 'Edge', 'Quantum', 'Neural', 'Algorithmic', 'Encrypted', 'Secure', 'Distributed', 'Decentralized', 'Artificial', 'Augmented', 'Enhanced', 'Optimized', 'Scalable', 'Agile', 'Responsive', 'Interactive', 'Dynamic', 'Static', 'Compiled', 'Interpreted', 'Functional', 'Procedural', 'Concurrent', 'Parallel', 'Asynchronous', 'Synchronous', 'Realtime', 'Configured', 'Installed', 'Updated', 'Patched', 'Debugged', 'Tested', 'Monitored', 'Logged', 'Cached', 'Indexed', 'Searched', 'Filtered', 'Sorted', 'Mapped', 'Reduced', 'Analyzed', 'Visualized', 'Modeled', 'Simulated', 'Emulated', 'Virtualized', 'Containerized', 'Orchestrated', 'Modular', 'Layered', 'Hierarchic', 'Licensed', 'Portable', 'Compatible', 'Advanced', 'Basic', 'Legacy', 'Modern', 'Serverless', 'Lowlevel', 'Highlevel', 'Objectoriented', 'Eventdriven', 'Datadriven', 'Statistical', 'Deterministic', 'Probabilistic', 'Predictive', 'Adaptive', 'Learning', 'Autonomous', 'Cognitive', 'Immersive', 'Graphic', 'Compute', 'Efficient', 'Lightweight', 'Heavyweight', 'Fast', 'Slow', 'Simple', 'Complex', 'Stable', 'Unstable', 'Powerful', 'Weak', 'Secure', 'Unsafe'],
            'nouns' => ['Code', 'Algorithm', 'Function', 'Method', 'Class', 'Object', 'Variable', 'Array', 'Loop', 'Condition', 'Database', 'Server', 'Client', 'Browser', 'Framework', 'Library', 'API', 'SDK', 'IDE', 'Compiler', 'Debugger', 'Terminal', 'Console', 'Command', 'Script', 'Program', 'Application', 'Software', 'Hardware', 'System', 'Network', 'Protocol', 'Interface', 'Module', 'Package', 'Repository', 'Version', 'Branch', 'Commit', 'Merge', 'Deploy', 'Build', 'Test', 'Debug', 'Refactor', 'Optimize', 'Scale', 'Monitor', 'Log', 'Cache', 'Session', 'Cookie', 'Token', 'Key', 'Hash', 'Encryption', 'Security', 'Authentication', 'Authorization', 'Validation', 'Sanitization', 'Injection', 'XSS', 'CSRF', 'SQL', 'NoSQL', 'JSON', 'XML', 'HTML', 'CSS', 'JavaScript', 'Python', 'Java', 'CPlusPlus', 'CSharp', 'PHP', 'Ruby', 'Go', 'Rust', 'Swift', 'Kotlin', 'TypeScript', 'React', 'Angular', 'Vue', 'Node', 'Express', 'Django', 'Flask', 'Laravel', 'Spring', 'Docker', 'Kubernetes', 'AWS', 'Azure', 'GCP', 'Firebase', 'MongoDB', 'MySQL', 'PostgreSQL', 'Redis', 'Elasticsearch', 'Apache', 'Nginx', 'Linux', 'Windows', 'MacOS', 'Android', 'iOS', 'Vim', 'Nano', 'Ufw', 'Git', 'Blockchain', 'Bitcoin', 'Ethereum', 'Solidity', 'Bit', 'Byte', 'Pixel', 'Bytecode', 'Firmware', 'Driver', 'Chip', 'Circuit', 'Board', 'Processor', 'GPU', 'TPU', 'Memory', 'Storage', 'Disk', 'SSD', 'HDD', 'Virtual', 'Container', 'Instance', 'Cluster', 'Grid', 'Mesh', 'Topology', 'Bandwidth', 'Latency', 'Throughput', 'Firewall', 'Proxy', 'VPN', 'Router', 'Switch', 'Hub', 'Bridge', 'SSL', 'SSH', 'FTP', 'HTTP', 'HTTPS', 'AI', 'ML', 'MachineLearning', 'DeepLearning', 'NeuralNet', 'Model', 'Dataset', 'Training', 'Inference', 'Prediction', 'Tensor', 'Notebook', 'Colab', 'Jupyter', 'Kernel', 'Thread', 'Process', 'Semaphore', 'Mutex', 'Queue', 'Stack', 'Heap', 'Pointer', 'Reference', 'Linker', 'Loader', 'Registry', 'Shell', 'Batch', 'Cloudflare', 'OpenAI', 'Chatbot', 'Agent', 'Crawler', 'Indexer', 'Recommender', 'Searcher', 'Renderer', 'Shader', 'PixelBuffer', 'Frame', 'Viewport', 'UI', 'UX', 'Frontend', 'Backend', 'Middleware', 'Fullstack', 'DevOps', 'SRE', 'Checkpoint', 'Rollback', 'Snapshot', 'Namespace', 'Daemon', 'Cron', 'Scheduler', 'Event', 'Trigger', 'Hook', 'Listener', 'Stream', 'Pipeline', 'Workflow', 'Orchestrator', 'Aggregator', 'Balancer', 'Router', 'Gateway', 'Adapter', 'Transformer', 'Serializer', 'Deserializer', 'Marshaller', 'Unmarshaller', 'Proxy', 'Stub', 'Mock', 'Spy', 'Fuzzer', 'Profiler', 'Tracer', 'Analyzer', 'Visualizer']
        ],
        'Elements and Chemistry' => [
            'adjectives' => ['Atomic', 'Molecular', 'Chemical', 'Organic', 'Inorganic', 'Synthetic', 'Natural', 'Pure', 'Mixed', 'Compound', 'Elementary', 'Metallic', 'Nonmetallic', 'Ionic', 'Covalent', 'Polar', 'Nonpolar', 'Acidic', 'Basic', 'Neutral', 'Oxidized', 'Reduced', 'Catalytic', 'Reactive', 'Stable', 'Unstable', 'Radioactive', 'Crystalline', 'Amorphous', 'Solid', 'Liquid', 'Gaseous', 'Plasma', 'Sublimed', 'Dissolved', 'Precipitated', 'Filtered', 'Distilled', 'Purified', 'Explosive', 'Combustible', 'Flammable', 'Corrosive', 'Toxic', 'Hazardous', 'Irritant', 'Volatile', 'Inert', 'Noble', 'Alkaline', 'Halogenated', 'Chlorinated', 'Oxidizing', 'Reducing', 'Endothermic', 'Exothermic', 'Isotopic', 'Isomeric', 'Homogeneous', 'Heterogeneous', 'Transparent', 'Translucent', 'Opaque', 'Conductive', 'Insulating', 'Semiconducting', 'Magnetic', 'Paramagnetic', 'Diamagnetic', 'Ferromagnetic', 'Allotropic', 'Colloidal', 'Viscous', 'Dense', 'Lightweight', 'Rarefied', 'Supersaturated', 'Concentrated', 'Dilute', 'Soluble', 'Insoluble', 'Miscible', 'Immiscible', 'Hydrated', 'Dehydrated', 'Anhydrous', 'Combining', 'Bonded', 'Adsorbed', 'Absorbed', 'Diffusive', 'Permeable', 'Impermeable', 'Electrolytic', 'Galvanic', 'Photochemical', 'Biochemical'],
            'nouns' => ['Hydrogen', 'Helium', 'Lithium', 'Beryllium', 'Boron', 'Carbon', 'Nitrogen', 'Oxygen', 'Fluorine', 'Neon', 'Sodium', 'Magnesium', 'Aluminum', 'Silicon', 'Phosphorus', 'Sulfur', 'Chlorine', 'Argon', 'Potassium', 'Calcium', 'Iron', 'Copper', 'Zinc', 'Silver', 'Gold', 'Mercury', 'Lead', 'Uranium', 'Plutonium', 'Radium', 'Krypton', 'Xenon', 'Radon', 'Element', 'Atom', 'Molecule', 'Ion', 'Electron', 'Proton', 'Neutron', 'Nucleus', 'Shell', 'Orbital', 'Bond', 'Reaction', 'Catalyst', 'Enzyme', 'Protein', 'Amino', 'Acid', 'Base', 'Salt', 'Oxide', 'Hydroxide', 'Carbonate', 'Sulfate', 'Nitrate', 'Phosphate', 'Chloride', 'Fluoride', 'Bromide', 'Iodide', 'Methane', 'Ethane', 'Propane', 'Butane', 'Benzene', 'Ethanol', 'Methanol', 'Acetone', 'Glucose', 'Fructose', 'Sucrose', 'Cellulose', 'Starch', 'DNA', 'RNA', 'ATP', 'Hemoglobin', 'Insulin', 'Adrenaline', 'Dopamine', 'Serotonin', 'Caffeine', 'Nicotine', 'Aspirin', 'Penicillin', 'Vitamin', 'Mineral', 'Crystal', 'Diamond', 'Graphite', 'Polymer', 'Plastic', 'Rubber', 'Glass', 'Ceramic', 'Metal', 'Alloy', 'Steel', 'Bronze', 'Brass', 'Earth', 'Water', 'Fire', 'Air', 'Sand', 'Clay', 'Rock', 'Fossil', 'Isotope', 'Mixture', 'Solution', 'Solvent', 'Solute', 'Compound', 'Sample', 'Specimen', 'Substance', 'Material', 'Extract', 'Residue', 'Powder', 'Granule', 'Pellet', 'Fiber', 'Particle', 'Micelle', 'Colloid', 'Gel', 'Foam', 'Emulsion', 'Suspension', 'Slurry', 'Gas', 'Liquid', 'Solid', 'Plasma', 'Precipitate', 'Crystal lattice', 'Quartz', 'Sulfuric acid', 'Nitric acid', 'Hydrochloric acid', 'Ammonia', 'Urea', 'Cholesterol', 'Hormone', 'Steroid', 'Lipid', 'Peptide', 'Antibody', 'Antigen', 'Pathogen', 'Bacterium', 'Virus', 'Fungus', 'Yeast', 'Enzyme complex', 'Receptor']
        ],
        'Things' => [
            'adjectives' => ['Tiny', 'Small', 'Mini', 'Micro', 'Slim', 'Light', 'Narrow', 'Short', 'Thin', 'Sleek', 'Compact', 'Portable', 'Handy', 'Neat', 'Trim', 'Dainty', 'Petite', 'Fine', 'Delicate', 'Subtle', 'Wide', 'Big', 'Large', 'Huge', 'Giant', 'Massive', 'Colossal', 'Enormous', 'Gigantic', 'Vast', 'Immense', 'Bulky', 'Heavy', 'Thick', 'Solid', 'Strong', 'Sturdy', 'Robust', 'Durable', 'Tough', 'Hard', 'Soft', 'Smooth', 'Rough', 'Sharp', 'Blunt', 'Curved', 'Straight', 'Round', 'Square', 'Flat', 'Deep', 'Shallow', 'Empty', 'Full', 'Clean', 'Dirty', 'New', 'Old', 'Modern', 'Ancient', 'Fresh', 'Stale', 'Cold', 'Warm', 'Cool', 'Wet', 'Dry', 'Bright', 'Dark', 'Colorful', 'Plain', 'Shiny', 'Dull', 'Transparent', 'Opaque', 'Clear', 'Cloudy', 'Fast', 'Slow', 'Quick', 'Rapid', 'Swift', 'Gradual', 'Instant', 'Immediate', 'Delayed', 'Early', 'Late', 'High', 'Low', 'Tall', 'Long', 'Brief', 'Permanent', 'Temporary', 'Flexible', 'Rigid', 'Loose', 'Tight', 'Open', 'Closed', 'Folded', 'Unfolded', 'Locked', 'Unlocked', 'Sealed', 'Unsealed', 'Wrapped', 'Unwrapped', 'Packed', 'Unpacked', 'Assembled', 'Disassembled', 'Fixed', 'Broken', 'Working', 'Nonworking', 'Functional', 'Nonfunctional', 'Operational', 'Nonoperational', 'Active', 'Inactive', 'On', 'Off', 'Connected', 'Disconnected', 'Plugged', 'Unplugged', 'Charged', 'Uncharged', 'Powered', 'Unpowered', 'Digital', 'Analog', 'Electronic', 'Mechanical', 'Manual', 'Automatic', 'Wireless', 'Wired', 'Smart', 'Dumb', 'Intelligent', 'Simple', 'Complex', 'Advanced', 'Basic', 'Standard', 'Custom', 'Unique', 'Common', 'Expensive', 'Cheap', 'Affordable', 'Luxury', 'Economy', 'Professional', 'Casual', 'Formal', 'Sporty', 'Vintage', 'Retro', 'Classic', 'Trendy', 'Stylish', 'Fashionable', 'Cool', 'Popular', 'Famous', 'Legendary', 'Iconic', 'Futuristic', 'Sci-fi', 'Fantasy', 'Exotic', 'Ordinary', 'Generic', 'Specific', 'Special', 'General', 'Rare', 'Frequent', 'Occasional', 'Regular', 'Constant', 'Consistent', 'Variable', 'Optional', 'Mandatory', 'Essential', 'Critical', 'Important', 'Useful', 'Useless', 'Necessary', 'Unnecessary', 'Positive', 'Negative', 'Neutral', 'Constructive', 'Destructive', 'Safe', 'Dangerous', 'Risky', 'Secure', 'Stable', 'Unstable', 'Sensitive', 'Insensitive', 'Precise', 'Imprecise', 'Accurate', 'Inaccurate', 'Exact', 'Approximate', 'Reliable', 'Unreliable', 'Valid', 'Invalid', 'Legal', 'Illegal', 'Correct', 'Incorrect', 'True', 'False', 'Real', 'Fake', 'Authentic', 'Artificial', 'Original', 'Duplicate', 'Primary', 'Secondary'],
            'nouns' => ['Pin', 'Needle', 'Wire', 'Thread', 'String', 'Line', 'Strip', 'Blade', 'Edge', 'Razor', 'Sword', 'Knife', 'Dagger', 'Arrow', 'Spear', 'Rod', 'Stick', 'Twig', 'Branch', 'Stem', 'Table', 'Chair', 'Desk', 'Shelf', 'Box', 'Case', 'Bag', 'Pouch', 'Wallet', 'Purse', 'Belt', 'Strap', 'Hook', 'Clip', 'Latch', 'Lock', 'Key', 'Button', 'Switch', 'Knob', 'Dial', 'Gauge', 'Meter', 'Screen', 'Panel', 'Board', 'Card', 'Chip', 'Disk', 'Drive', 'Wheel', 'Tire', 'Pedal', 'Brake', 'Clutch', 'Gear', 'Lever', 'Handle', 'Hinge', 'Spring', 'Bolt', 'Nut', 'Screw', 'Nail', 'Rivet', 'Pinch', 'Grip', 'Hold', 'Catch', 'Wrench', 'Hammer', 'Saw', 'Drill', 'File', 'Chisel', 'Plane', 'Brush', 'Broom', 'Mop', 'Sponge', 'Cloth', 'Towel', 'Rag', 'Mat', 'Rug', 'Carpet', 'Curtain', 'Blind', 'Shade', 'Lamp', 'Light', 'Bulb', 'Candle', 'Torch', 'Flame', 'Fire', 'Heat', 'Cooler', 'Fan', 'Heater', 'Aircon', 'Fridge', 'Oven', 'Stove', 'Microwave', 'Toaster', 'Kettle', 'Pot', 'Pan', 'Bowl', 'Plate', 'Cup', 'Mug', 'Glass', 'Bottle', 'Jar', 'Glove', 'Fork', 'Spoon', 'Chopstick', 'Napkin', 'Sofa', 'Couch', 'Bed', 'Pillow', 'Blanket', 'Sheet', 'Mattress', 'Frame', 'Mirror', 'Picture', 'Clock', 'Watch', 'Phone', 'Tablet', 'Laptop', 'Computer', 'Printer', 'Camera', 'Speaker', 'Headphone', 'Microphone', 'Remote', 'Console', 'Gamepad', 'Joystick', 'Car', 'Bike', 'Bus', 'Truck', 'Train', 'Plane', 'Boat', 'Ship', 'Cabinet', 'Drawer', 'Wardrobe', 'Closet', 'Bucket', 'Bin', 'Basket', 'Crate', 'Stool', 'Bench', 'Stair', 'Ladder', 'Roof', 'Window', 'Door', 'Fence', 'Gate', 'Keychain', 'Padlock', 'Calendar', 'Notebook', 'Paper', 'Envelope', 'Pen', 'Pencil', 'Marker', 'Eraser', 'Sharpener', 'Highlighter', 'Chalk', 'Glue', 'Tape', 'Scissors', 'Ruler', 'Compass', 'Calculator', 'Stapler', 'Clipper', 'Cutter', 'Shovel', 'Hoe', 'Rake', 'Pitchfork', 'Spade', 'Trolley', 'Cart', 'Basketball', 'Football', 'Tennis', 'Bat', 'Racket', 'Skate', 'Helmet', 'Mask', 'Apron', 'Boot', 'Shoe', 'Slipper', 'Hat', 'Cap', 'Coat', 'Jacket', 'Shirt', 'Pants', 'Dress', 'Skirt', 'Suit', 'Tie', 'Scarf', 'Glasses', 'Sunglasses', 'Watch', 'Bracelet', 'Necklace', 'Ring', 'Earring', 'Crown', 'Tiara', 'Brooch', 'Badge', 'Medal', 'Trophy', 'Statue', 'Figure', 'Model', 'Toy', 'Doll', 'Puzzle', 'Game', 'Book', 'Magazine', 'Comic', 'Map', 'Poster', 'Flyer', 'Ticket', 'Cardigan']
        ],
        'Body and Health' => [
            'adjectives' => ['Strong', 'Healthy', 'Fit', 'Agile', 'Quick', 'Swift', 'Brave', 'Bold', 'Fearless', 'Energetic', 'Vigorous', 'Robust', 'Sturdy', 'Tough', 'Resilient', 'Enduring', 'Durable', 'Powerful', 'Mighty', 'Muscular', 'Athletic', 'Lean', 'Toned', 'Sleek', 'Supple', 'Flexible', 'Nimble', 'Lithe', 'Graceful', 'Elegant', 'Weak', 'Fragile', 'Delicate', 'Frail', 'Slender', 'Slim', 'Skinny', 'Chubby', 'Plump', 'Round', 'Curvy', 'Bulky', 'Heavy', 'Massive', 'Giant', 'Tiny', 'Small', 'Mini', 'Micro', 'Short', 'Tall', 'Huge', 'Gigantic', 'Colossal', 'Enormous', 'Vast', 'Immense', 'Big', 'Active', 'Rested'],
            'nouns' => ['Head', 'Face', 'Eye', 'Ear', 'Nose', 'Mouth', 'Tooth', 'Tongue', 'Lip', 'Cheek', 'Chin', 'Neck', 'Shoulder', 'Arm', 'Elbow', 'Wrist', 'Hand', 'Finger', 'Thumb', 'Palm', 'Back', 'Chest', 'Stomach', 'Waist', 'Hip', 'Leg', 'Knee', 'Ankle', 'Foot', 'Toe', 'Brain', 'Heart', 'Lung', 'Liver', 'Kidney', 'Muscle', 'Bone', 'Skin', 'Hair', 'Nail', 'Fist', 'Forearm', 'Bicep', 'Tricep', 'Spine', 'Buttock', 'Thigh', 'Calf', 'Heel', 'Blood', 'Vein', 'Artery', 'Nerve', 'Cell', 'Tissue', 'Organ', 'Body', 'Torso', 'Limb', 'Joint', 'Cartilage', 'Tendon', 'Ligament', 'Rib', 'Skull', 'Jaw', 'Forehead', 'Eyebrow', 'Eyelash', 'Pupil', 'Iris', 'Nostril', 'Gum', 'Throat', 'Voice', 'Breath', 'Pulse', 'Heartbeat', 'Temperature', 'Fever', 'Pain', 'Ache', 'Wound', 'Scar', 'Bruise', 'Cut', 'Burn', 'Swelling', 'Infection', 'Disease', 'Illness', 'Symptom', 'Medicine', 'Treatment', 'Therapy', 'Surgery', 'Doctor', 'Nurse', 'Patient', 'Health']
        ],
        'Nature' => [
            'adjectives' => ['Wild', 'Free', 'Green', 'Fresh', 'Pure', 'Natural', 'Earthy', 'Rustic', 'Rural', 'Woodland','Forest', 'Jungle', 'Tropical', 'Savanna', 'Desert', 'Arid', 'Mountainous', 'Hilly', 'Valley', 'Riverine', 'Coastal', 'Marine', 'Oceanic', 'Lakeside', 'Wetland', 'Swampy', 'Misty', 'Foggy', 'Sunny', 'Cloudy', 'Rainy', 'Stormy', 'Windy', 'Calm', 'Peaceful', 'Tranquil', 'Serene', 'Clear', 'Bright', 'Dark', 'Shadowy', 'Shady', 'Blooming', 'Flourishing', 'Thriving', 'Vibrant', 'Lush', 'Fertile', 'Barren', 'Rocky', 'Sandy', 'Icy', 'Snowy', 'Frosty', 'Chilly', 'Warm', 'Hot', 'Cool', 'Moist', 'Dry', 'Torrential', 'Glacial', 'Volcanic', 'Seismic', 'Earthen', 'Clayey', 'Muddy', 'Dusty', 'Leafy', 'Floral', 'Fragrant', 'Aromatic', 'Spicy', 'Herbal', 'Medicinal', 'Berry-like', 'Woody', 'Grassy', 'Mossy', 'Ferny', 'Petalled', 'Rooted', 'Sapling', 'Ancient', 'Primeval', 'Pristine', 'Untamed', 'Expansive', 'Boundless', 'Infinite', 'Golden', 'Silvery', 'Azure', 'Verdant', 'Bloomy', 'Snowcapped', 'Crystalline', 'Shimmering', 'Glistening', 'Iridescent'],
            'nouns' => ['Tree', 'Flower', 'Grass', 'Leaf', 'Branch', 'Root', 'Bark', 'Seed', 'Fruit', 'Berry', 'Mushroom', 'Fern', 'Moss', 'Vine', 'Bush', 'Shrub', 'Cactus', 'Pinecone', 'Acorn', 'Stream', 'River', 'Lake', 'Pond', 'Ocean', 'Sea', 'Beach', 'Cliff', 'Hill', 'Mountain', 'Valley', 'Cave', 'Desert', 'Dune', 'Rock', 'Stone', 'Pebble', 'Sandstone', 'Apple', 'Banana', 'Orange', 'Grape', 'Lemon', 'Lime', 'Cherry', 'Peach', 'Pear', 'Plum', 'Melon', 'Coconut', 'Pineapple', 'Mango', 'Papaya', 'Kiwi', 'Nut', 'Almond', 'Walnut', 'Pistachio', 'Cashew', 'Hazelnut', 'Chestnut', 'Fox', 'Wolf', 'Tiger', 'Lion', 'Bear', 'Deer', 'Rabbit', 'Squirrel', 'Eagle', 'Hawk', 'Owl', 'Falcon', 'Vulture', 'Parrot', 'Sparrow', 'Dove', 'Pigeon', 'Fish', 'Shark', 'Whale', 'Dolphin', 'Seal', 'Otter', 'Frog', 'Toad', 'Lizard', 'Snake', 'Turtle', 'Insect', 'Butterfly', 'Bee', 'Ant', 'Spider', 'Beetle', 'Worm', 'Caterpillar', 'Dragonfly', 'Grasshopper', 'Cricket', 'Snail', 'Slug', 'Crab', 'Lobster', 'Shrimp', 'Jellyfish', 'Coral', 'Starfish', 'Clam', 'Oyster', 'Mussel', 'Scallop', 'Waterfall', 'Swamp', 'Marsh', 'Jungle', 'Forest', 'Canyon', 'Viper', 'Scorpion', 'Cobra', 'Python', 'Anaconda', 'Elephant', 'Giraffe', 'Zebra', 'Rhino', 'Hippo', 'Crocodile', 'Alligator', 'Penguin', 'Flamingo', 'Peacock', 'Swan', 'Duck', 'Goose', 'Heron', 'Pelican', 'Seagull', 'Albatross', 'Hummingbird', 'Robin', 'Cardinal', 'Woodpecker', 'Raven', 'Crow', 'Magpie', 'Kingfisher', 'Toucan', 'Macaw', 'Cockatoo']
        ],
        'Space and Time' => [
            'adjectives' => ['Fast', 'Quick', 'Swift', 'Rapid', 'Speedy', 'Instant', 'Immediate', 'Delayed', 'Slow', 'Steady', 'Gradual', 'Constant', 'Variable', 'Fixed', 'Flexible', 'Adjustable', 'Measurable', 'Quantifiable', 'Precise', 'Accurate', 'Exact', 'Approximate', 'Relative', 'Absolute', 'Linear', 'Nonlinear', 'Cyclic', 'Periodic', 'Random', 'Chaotic', 'Stable', 'Unstable', 'Centi', 'Milli', 'Micro', 'Nano', 'Pico', 'Femto', 'Atto', 'Zepto', 'Yocto', 'Kilo', 'Mega', 'Giga', 'Tera', 'Peta', 'Exa', 'Zetta', 'Yotta', 'Spatial', 'Temporal', 'Cosmic', 'Celestial', 'Astral', 'Interstellar', 'Galactic', 'Universal', 'Multidimensional', 'Chronal', 'Eternal', 'Infinite', 'Finite', 'Relative', 'Absolute', 'Quantum', 'Gravitational', 'Relativistic', 'Spacetime', 'Extradimensional', 'Event', 'Horizon', 'Continuum', 'Singularity', 'Dimension', 'Parallel', 'Alternate', 'Future', 'Past', 'Present', 'Eternal', 'Timeless', 'Momentary', 'Ephemeral', 'Transient', 'Fleeting', 'Endless', 'Boundless', 'Limitless'],
            'nouns' => ['Second', 'Minute', 'Hour', 'Day', 'Week', 'Month', 'Year', 'Decade', 'Century', 'Millennium', 'Moment', 'Instant', 'Eon', 'Epoch', 'Era', 'Age', 'Cycle', 'Phase', 'Period', 'Interval', 'Duration', 'Span', 'Stretch', 'Term', 'Session', 'Shift', 'Meter', 'Kilometer', 'Gram', 'Kilogram', 'Liter', 'Milliliter', 'Celsius', 'Fahrenheit', 'Kelvin', 'Watt', 'Volt', 'Ampere', 'Ohm', 'Joule', 'Newton', 'Pascal', 'Hertz', 'Lumen', 'Lux', 'Candela', 'Metric', 'System', 'Mass', 'Volume', 'Length', 'Distance', 'Speed', 'Velocity', 'Acceleration', 'Force', 'Energy', 'Power', 'Work', 'Heat', 'Temperature', 'Pressure', 'Frequency', 'Wavelength', 'Amplitude', 'Intensity', 'Density', 'Gravity', 'Magnetism', 'Electricity', 'Radiation', 'Lightyear', 'Parsec', 'Spacetime', 'Dimension', 'Continuum', 'Cosmos', 'Universe', 'Multiverse', 'Galaxy', 'Nebula', 'Singularity', 'Blackhole', 'Wormhole', 'Horizon', 'Cone', 'Worldline', 'Timeline', 'Causality', 'Graviton', 'Manifold', 'Curvature', 'Relativity', 'Quantum', 'Particle', 'Photon', 'Electron', 'Proton', 'Neutron', 'Atom', 'Molecule', 'Quark', 'Boson', 'Fermion', 'Lepton', 'Baryon', 'Meson', 'Antimatter', 'Plasma', 'Field', 'Wave', 'String', 'Loop', 'Vacuum', 'Void', 'Space', 'Time', 'Motion', 'Rest', 'Frame', 'Reference', 'Observer', 'Event', 'Coordinate', 'Vector', 'Tensor', 'Matrix', 'Equation', 'Formula', 'Constant', 'Variable', 'Function', 'Derivative', 'Integral', 'Infinity', 'Zero', 'Unity', 'Symmetry', 'Asymmetry', 'Invariance', 'Transformation', 'Translation', 'Rotation', 'Reflection', 'Inversion', 'Dilation', 'Contraction', 'Expansion', 'Compression', 'Deformation']
        ]
    ];

    // General adjectives (colors, shapes, sizes, etc.)
    private $general_adjectives = [
        // Colors
        'Red', 'Blue', 'Green', 'Yellow', 'Purple', 'Orange', 'Pink', 'Black', 'White', 'Gray', 'Brown', 'Silver', 'Gold', 'Violet', 'Crimson', 'Azure', 'Emerald', 'Amber', 'Ruby', 'Sapphire',
        // Shapes & Sizes
        'Round', 'Square', 'Big', 'Small', 'Tiny', 'Huge', 'Giant', 'Mini', 'Mega', 'Micro', 'Large', 'Little', 'Tall', 'Short', 'Wide', 'Thin', 'Thick', 'Long', 'Curved', 'Straight',
        // Textures & Materials
        'Smooth', 'Rough', 'Soft', 'Hard', 'Fluffy', 'Silky', 'Fuzzy', 'Glossy', 'Matte', 'Shiny', 'Metallic', 'Wooden', 'Stone', 'Crystal', 'Glass', 'Steel', 'Iron', 'Velvet', 'Marble', 'Diamond',
        // Temperatures & Weather
        'Hot', 'Cold', 'Warm', 'Cool', 'Frozen', 'Burning', 'Icy', 'Fiery', 'Sunny', 'Cloudy', 'Stormy', 'Misty', 'Foggy', 'Windy', 'Rainy', 'Snowy', 'Frosty', 'Blazing', 'Chilly', 'Tropical',
        // Time & Speed
        'Fast', 'Slow', 'Quick', 'Rapid', 'Swift', 'Speedy', 'Instant', 'Ancient', 'Modern', 'Old', 'New', 'Young', 'Fresh', 'Vintage', 'Classic', 'Retro', 'Future', 'Past', 'Present', 'Eternal',
        // Qualities & States
        'Perfect', 'Pure', 'Simple', 'Complex', 'Easy', 'Tough', 'Gentle', 'Bold', 'Calm', 'Active', 'Quiet', 'Loud', 'Bright', 'Dark', 'Light', 'Heavy', 'Empty', 'Full', 'Open', 'Closed'
    ];

    // Numbers and symbols for additional options
    private $numbers = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '69', '99', '123', '777', '2024', '2025'];
    private $symbols = ['_', '-', '.', 'X', 'Z'];

    public function generateUsernames($options = []) {
        // Default options
        $defaults = [
            'themes' => ['Fantasy'], // Changed from 'theme' to 'themes' array
            'min_length' => 6,
            'max_length' => 20,
            'count' => 10,
            'include_numbers' => false,
            'include_symbols' => false,
            'capitalize' => true,
            'avoid_repetition' => true,
            'use_all_adjectives' => false,
            'use_general_adjectives' => false,
            'custom_words' => []
        ];

        $options = array_merge($defaults, $options);
        
        // Ensure themes is always an array
        if (isset($options['theme']) && !isset($options['themes'])) {
            // Backward compatibility: convert single theme to themes array
            $options['themes'] = [$options['theme']];
        }
        if (!is_array($options['themes']) || empty($options['themes'])) {
            $options['themes'] = ['Fantasy'];
        }

        $usernames = [];
        $used_combinations = [];

        // Get word lists from selected themes
        $adjectives = [];
        $nouns = [];
        
        // Combine words from all selected themes
        foreach ($options['themes'] as $theme) {
            if (isset($this->themes[$theme])) {
                $adjectives = array_merge($adjectives, $this->themes[$theme]['adjectives']);
                $nouns = array_merge($nouns, $this->themes[$theme]['nouns']);
            }
        }
        
        // Remove duplicates
        $adjectives = array_unique($adjectives);
        $nouns = array_unique($nouns);
        
        // Handle "Use All Adjectives" option
        if ($options['use_all_adjectives']) {
            // Combine all adjectives from all themes
            $all_adjectives = [];
            foreach ($this->themes as $theme_data) {
                $all_adjectives = array_merge($all_adjectives, $theme_data['adjectives']);
            }
            // Remove duplicates and keep only unique adjectives
            $adjectives = array_unique($all_adjectives);
            // Keep nouns from selected themes only
        }

        // Add general adjectives if requested
        if ($options['use_general_adjectives']) {
            $adjectives = array_merge($adjectives, $this->general_adjectives);
            // Remove duplicates
            $adjectives = array_unique($adjectives);
        }

        // Add custom words if provided
        if (!empty($options['custom_words'])) {
            $custom_array = array_map('trim', explode(',', $options['custom_words']));
            $adjectives = array_merge($adjectives, $custom_array);
            $nouns = array_merge($nouns, $custom_array);
        }

        $attempts = 0;
        // Increase max attempts for restrictive length constraints
        $length_range = $options['max_length'] - $options['min_length'] + 1;
        $difficulty_multiplier = ($length_range <= 5) ? 50 : (($length_range <= 10) ? 25 : 15);
        $max_attempts = $options['count'] * $difficulty_multiplier;

        while (count($usernames) < $options['count'] && $attempts < $max_attempts) {
            $attempts++;
            
            // Generate base username
            $adjective = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            
            // Create combination key for avoiding repetition
            $combination_key = strtolower($adjective . '_' . $noun);
            
            if ($options['avoid_repetition'] && in_array($combination_key, $used_combinations)) {
                continue;
            }

            $username = $adjective . $noun;

            // Add numbers if requested
            if ($options['include_numbers']) {
                if (rand(0, 1)) { // 50% chance
                    $username .= $this->numbers[array_rand($this->numbers)];
                }
            }

            // Add symbols if requested
            if ($options['include_symbols']) {
                if (rand(0, 2) === 0) { // 33% chance
                    $symbol = $this->symbols[array_rand($this->symbols)];
                    $position = rand(0, 1);
                    if ($position === 0) {
                        $username = $adjective . $symbol . $noun;
                    } else {
                        $username = $username . $symbol;
                    }
                }
            }

            // Apply capitalization
            if (!$options['capitalize']) {
                $username = strtolower($username);
            }

            // Check length constraints
            if (strlen($username) >= $options['min_length'] && strlen($username) <= $options['max_length']) {
                $usernames[] = $username;
                if ($options['avoid_repetition']) {
                    $used_combinations[] = $combination_key;
                }
            }
        }

        // If we couldn't generate the full count, it's likely due to restrictive constraints
        return $usernames;
    }

    public function getAvailableThemes() {
        return array_keys($this->themes);
    }

    public function validateOptions($options) {
        $errors = [];

        if (isset($options['min_length']) && (!is_numeric($options['min_length']) || $options['min_length'] < 3)) {
            $errors[] = 'Minimum length must be at least 3 characters';
        }

        if (isset($options['max_length']) && (!is_numeric($options['max_length']) || $options['max_length'] > 50)) {
            $errors[] = 'Maximum length cannot exceed 50 characters';
        }

        if (isset($options['min_length']) && isset($options['max_length']) && $options['min_length'] > $options['max_length']) {
            $errors[] = 'Minimum length cannot be greater than maximum length';
        }

        if (isset($options['count']) && (!is_numeric($options['count']) || $options['count'] < 1 || $options['count'] > 50)) {
            $errors[] = 'Count must be between 1 and 50';
        }

        // Validate themes
        if (isset($options['themes'])) {
            if (!is_array($options['themes']) || empty($options['themes'])) {
                $errors[] = 'At least one theme must be selected';
            } else {
                foreach ($options['themes'] as $theme) {
                    if (!array_key_exists($theme, $this->themes)) {
                        $errors[] = 'Invalid theme selected: ' . htmlspecialchars($theme, ENT_QUOTES, 'UTF-8');
                    }
                }
            }
        }
        
        // Backward compatibility for single theme
        if (isset($options['theme']) && !array_key_exists($options['theme'], $this->themes)) {
            $errors[] = 'Invalid theme selected';
        }

        return $errors;
    }
}

// Initialize generator
$generator = new UsernameGenerator();

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Support both JSON POST and GET parameters
$options = [];

// Handle themes (multiple themes support)
if (isset($input['themes'])) {
    $options['themes'] = is_array($input['themes']) ? $input['themes'] : [$input['themes']];
} elseif (isset($_GET['themes'])) {
    // For GET requests, themes can be comma-separated string or array
    if (is_array($_GET['themes'])) {
        $options['themes'] = $_GET['themes'];
    } else {
        $options['themes'] = array_map('trim', explode(',', $_GET['themes']));
    }
} elseif (isset($input['theme']) || isset($_GET['theme'])) {
    // Backward compatibility for single theme
    $single_theme = $input['theme'] ?? $_GET['theme'] ?? 'Fantasy';
    $options['themes'] = [$single_theme];
} else {
    $options['themes'] = ['Fantasy'];
}

$options['min_length'] = intval($input['min_length'] ?? $_GET['min_length'] ?? 6);
$options['max_length'] = intval($input['max_length'] ?? $_GET['max_length'] ?? 20);
$options['count'] = intval($input['count'] ?? $_GET['count'] ?? 10);
$options['include_numbers'] = filter_var($input['include_numbers'] ?? $_GET['include_numbers'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['include_symbols'] = filter_var($input['include_symbols'] ?? $_GET['include_symbols'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['capitalize'] = filter_var($input['capitalize'] ?? $_GET['capitalize'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['avoid_repetition'] = filter_var($input['avoid_repetition'] ?? $_GET['avoid_repetition'] ?? true, FILTER_VALIDATE_BOOLEAN);
$options['use_all_adjectives'] = filter_var($input['use_all_adjectives'] ?? $_GET['use_all_adjectives'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['use_general_adjectives'] = filter_var($input['use_general_adjectives'] ?? $_GET['use_general_adjectives'] ?? false, FILTER_VALIDATE_BOOLEAN);
$options['custom_words'] = $input['custom_words'] ?? $_GET['custom_words'] ?? '';

// Handle special request for available themes
if (isset($_GET['action']) && $_GET['action'] === 'themes') {
    echo json_encode([
        'success' => true,
        'themes' => $generator->getAvailableThemes(),
        'theme_descriptions' => [
            'Fantasy' => 'Epic and mythical usernames for gaming and fantasy lovers',
            'Professional' => 'Suitable for business, LinkedIn, and professional networks',
            'Science and Space' => 'Science and space exploration themed usernames',
            'Computer Technology' => 'Tech and programming themed usernames',
            'Elements and Chemistry' => 'Science-inspired usernames with elements and compounds',
            'Things' => 'Everyday objects and items themed usernames',
            'Body and Health' => 'Body parts and health-themed usernames',
            'Nature' => 'Nature-inspired usernames with plants, animals, and landscapes',
            'Space and Time' => 'Usernames inspired by concepts of space and time'
        ]
    ]);
    exit();
}

// Validate options
$validation_errors = $generator->validateOptions($options);

if (!empty($validation_errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Validation failed',
        'messages' => $validation_errors
    ]);
    exit();
}

// Generate usernames
try {
    $usernames = $generator->generateUsernames($options);
    
    if (empty($usernames)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'No usernames could be generated',
            'message' => 'Try adjusting your length constraints or other options'
        ]);
        exit();
    }

    // Return successful response
    $response = [
        'success' => true,
        'data' => [
            'usernames' => $usernames,
            'count' => count($usernames),
            'options_used' => $options
        ],
        'generation_info' => [
            'themes' => $options['themes'],
            'theme_count' => count($options['themes']),
            'length_range' => $options['min_length'] . '-' . $options['max_length'] . ' characters',
            'features' => [
                'numbers' => $options['include_numbers'] ? 'included' : 'excluded',
                'symbols' => $options['include_symbols'] ? 'included' : 'excluded',
                'capitalization' => $options['capitalize'] ? 'enabled' : 'disabled'
            ]
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Add warning if fewer usernames generated than requested
    if (count($usernames) < $options['count']) {
        $response['warning'] = [
            'message' => 'Generated fewer usernames than requested due to restrictive constraints',
            'requested' => $options['count'],
            'generated' => count($usernames),
            'suggestion' => 'Try increasing max_length, decreasing min_length, or using more themes for better results'
        ];
    }

    echo json_encode($response, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => 'Failed to generate usernames'
    ]);
}
?>
