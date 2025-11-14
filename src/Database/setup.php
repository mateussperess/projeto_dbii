<?php

$bd = __DIR__ . "/projeto.sqlite";

try {
  $conn = new PDO("sqlite:$bd");
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  echo "banco criado e conectado com sucesso";
} catch (PDOException $e) {
  echo "Ocorreu um problema ao conectar com a base de dados: " . $e->getMessage();
}

try {

  // tabela Users
  $table_users = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    senha TEXT NOT NULL,
    telefone TEXT NOT NULL,
    isAdmin INTEGER NOT NULL DEFAULT 0
  )";

  $conn->exec($table_users);
  echo "Tabela 'users' criada com sucesso\n";

  // tabela Token
  $table_token = "CREATE TABLE IF NOT EXISTS token (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    token TEXT NOT NULL,
    expiredAt TEXT NOT NULL,
    id_user INTEGER NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
  )";

  $conn->exec($table_token);
  echo "Tabela 'token' criada com sucesso\n";

  // tabela Categorias
  $table_categorias = "CREATE TABLE IF NOT EXISTS categorias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    descricao TEXT NOT NULL
  )";

  $conn->exec($table_categorias);
  echo "Tabela 'categorias' criada com sucesso\n";

  // tabela Livros
  $table_livros = "CREATE TABLE IF NOT EXISTS livros (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT NOT NULL,
    autor TEXT NOT NULL,
    descricao TEXT,
    ano INTEGER,
    n_paginas INTEGER,
    isAlocated INTEGER NOT NULL DEFAULT 0,
    n_alocated INTEGER NOT NULL DEFAULT 0,
    id_genero INTEGER NOT NULL,
    FOREIGN KEY (id_genero) REFERENCES categorias(id) ON DELETE RESTRICT
  )";

  $conn->exec($table_livros);
  echo "Tabela 'livros' criada com sucesso\n";

  // tabela emprestimos
  $table_emprestimos = "CREATE TABLE IF NOT EXISTS emprestimos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idUser INTEGER NOT NULL,
    idLivro INTEGER NOT NULL,
    data_inicio TEXT NOT NULL,
    data_entrega TEXT,
    FOREIGN KEY (idUser) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (idLivro) REFERENCES livros(id) ON DELETE CASCADE
  )";

  $conn->exec($table_emprestimos);
  echo "Tabela 'emprestimos' criada com sucesso\n";

  echo "\nTodas as tabelas foram criadas com sucesso!";
} catch (PDOException $e) {
  echo "Ocorreu um erro ao criar as tabelas: " . $e->getMessage();
}

$categorias = [
  ["Ficção Científica"],
  ["Romance"],
  ["Suspense/Thriller"],
  ["Fantasia"],
  ["Biografia"],
  ["Autoajuda"],
  ["História"],
  ["Tecnologia"]
];

$livros = [
  // Ficção Científica (id_genero: 1)
  [
    'titulo' => 'Duna',
    'autor' => 'Frank Herbert',
    'descricao' => 'Em um futuro distante, nobres casas disputam o controle do planeta desértico Arrakis.',
    'ano' => 1965,
    'n_paginas' => 680,
    'id_genero' => 1
  ],
  [
    'titulo' => 'Neuromancer',
    'autor' => 'William Gibson',
    'descricao' => 'Um hacker é contratado para realizar o maior hack da história no ciberespaço.',
    'ano' => 1984,
    'n_paginas' => 271,
    'id_genero' => 1
  ],
  [
    'titulo' => 'Fundação',
    'autor' => 'Isaac Asimov',
    'descricao' => 'A queda de um império galáctico e a criação da Fundação para preservar o conhecimento.',
    'ano' => 1951,
    'n_paginas' => 255,
    'id_genero' => 1
  ],
  [
    'titulo' => 'O Fim da Eternidade',
    'autor' => 'Isaac Asimov',
    'descricao' => 'Uma organização controla as mudanças no tempo para melhorar a história humana.',
    'ano' => 1955,
    'n_paginas' => 224,
    'id_genero' => 1
  ],
  [
    'titulo' => 'Fahrenheit 451',
    'autor' => 'Ray Bradbury',
    'descricao' => 'Em uma sociedade onde livros são proibidos, um bombeiro questiona sua missão de queimá-los.',
    'ano' => 1953,
    'n_paginas' => 158,
    'id_genero' => 1
  ],

  // Romance (id_genero: 2)
  [
    'titulo' => 'Orgulho e Preconceito',
    'autor' => 'Jane Austen',
    'descricao' => 'Elizabeth Bennet e Mr. Darcy superam diferenças de classe e mal-entendidos.',
    'ano' => 1813,
    'n_paginas' => 432,
    'id_genero' => 2
  ],
  [
    'titulo' => 'O Morro dos Ventos Uivantes',
    'autor' => 'Emily Brontë',
    'descricao' => 'Uma história de amor obsessivo e vingança entre Heathcliff e Catherine.',
    'ano' => 1847,
    'n_paginas' => 416,
    'id_genero' => 2
  ],
  [
    'titulo' => 'Como Eu Era Antes de Você',
    'autor' => 'Jojo Moyes',
    'descricao' => 'Louisa Clark se torna cuidadora de Will Traynor e suas vidas se transformam.',
    'ano' => 2012,
    'n_paginas' => 368,
    'id_genero' => 2
  ],
  [
    'titulo' => 'Eleanor & Park',
    'autor' => 'Rainbow Rowell',
    'descricao' => 'Dois adolescentes marginalizados se apaixonam no ônibus escolar nos anos 80.',
    'ano' => 2013,
    'n_paginas' => 328,
    'id_genero' => 2
  ],
  [
    'titulo' => 'A Culpa é das Estrelas',
    'autor' => 'John Green',
    'descricao' => 'Hazel e Augustus, dois adolescentes com câncer, vivem um romance intenso.',
    'ano' => 2012,
    'n_paginas' => 288,
    'id_genero' => 2
  ],

  // Suspense/Thriller (id_genero: 3)
  [
    'titulo' => 'Garota Exemplar',
    'autor' => 'Gillian Flynn',
    'descricao' => 'O desaparecimento de Amy Dunne revela segredos perturbadores em seu casamento.',
    'ano' => 2012,
    'n_paginas' => 432,
    'id_genero' => 3
  ],
  [
    'titulo' => 'O Silêncio dos Inocentes',
    'autor' => 'Thomas Harris',
    'descricao' => 'Uma agente do FBI consulta o psicopata Hannibal Lecter para capturar um serial killer.',
    'ano' => 1988,
    'n_paginas' => 368,
    'id_genero' => 3
  ],
  [
    'titulo' => 'A Garota no Trem',
    'autor' => 'Paula Hawkins',
    'descricao' => 'Rachel observa um casal perfeito de seu trem até testemunhar algo chocante.',
    'ano' => 2015,
    'n_paginas' => 368,
    'id_genero' => 3
  ],
  [
    'titulo' => 'O Código Da Vinci',
    'autor' => 'Dan Brown',
    'descricao' => 'Robert Langdon investiga um assassinato que revela segredos do cristianismo.',
    'ano' => 2003,
    'n_paginas' => 480,
    'id_genero' => 3
  ],
  [
    'titulo' => 'A Mulher na Janela',
    'autor' => 'A.J. Finn',
    'descricao' => 'Anna Fox, reclusa em casa, testemunha um crime na casa vizinha.',
    'ano' => 2018,
    'n_paginas' => 448,
    'id_genero' => 3
  ],

  // Fantasia (id_genero: 4)
  [
    'titulo' => 'O Senhor dos Anéis',
    'autor' => 'J.R.R. Tolkien',
    'descricao' => 'Frodo Bolseiro deve destruir o Um Anel para salvar a Terra-média.',
    'ano' => 1954,
    'n_paginas' => 1178,
    'id_genero' => 4
  ],
  [
    'titulo' => 'Harry Potter e a Pedra Filosofal',
    'autor' => 'J.K. Rowling',
    'descricao' => 'Harry descobre ser um bruxo e inicia seus estudos em Hogwarts.',
    'ano' => 1997,
    'n_paginas' => 264,
    'id_genero' => 4
  ],
  [
    'titulo' => 'As Crônicas de Nárnia',
    'autor' => 'C.S. Lewis',
    'descricao' => 'Crianças descobrem um mundo mágico através de um guarda-roupa.',
    'ano' => 1950,
    'n_paginas' => 208,
    'id_genero' => 4
  ],
  [
    'titulo' => 'A Guerra dos Tronos',
    'autor' => 'George R.R. Martin',
    'descricao' => 'Famílias nobres disputam o Trono de Ferro nos Sete Reinos de Westeros.',
    'ano' => 1996,
    'n_paginas' => 694,
    'id_genero' => 4
  ],
  [
    'titulo' => 'O Nome do Vento',
    'autor' => 'Patrick Rothfuss',
    'descricao' => 'Kvothe narra sua jornada de órfão a lendário mago e músico.',
    'ano' => 2007,
    'n_paginas' => 662,
    'id_genero' => 4
  ],

  // Biografia (id_genero: 5)
  [
    'titulo' => 'Steve Jobs',
    'autor' => 'Walter Isaacson',
    'descricao' => 'A biografia autorizada do cofundador da Apple.',
    'ano' => 2011,
    'n_paginas' => 656,
    'id_genero' => 5
  ],
  [
    'titulo' => 'Eu Sou Malala',
    'autor' => 'Malala Yousafzai',
    'descricao' => 'A história da jovem paquistanesa que lutou pelo direito à educação.',
    'ano' => 2013,
    'n_paginas' => 336,
    'id_genero' => 5
  ],
  [
    'titulo' => 'Long Walk to Freedom',
    'autor' => 'Nelson Mandela',
    'descricao' => 'A autobiografia de Nelson Mandela sobre sua luta contra o apartheid.',
    'ano' => 1994,
    'n_paginas' => 656,
    'id_genero' => 5
  ],
  [
    'titulo' => 'Einstein: Sua Vida, Seu Universo',
    'autor' => 'Walter Isaacson',
    'descricao' => 'A vida do físico que revolucionou nossa compreensão do universo.',
    'ano' => 2007,
    'n_paginas' => 704,
    'id_genero' => 5
  ],
  [
    'titulo' => 'Minha História',
    'autor' => 'Michelle Obama',
    'descricao' => 'As memórias da ex-primeira-dama dos Estados Unidos.',
    'ano' => 2018,
    'n_paginas' => 448,
    'id_genero' => 5
  ],

  // Autoajuda (id_genero: 6)
  [
    'titulo' => 'O Poder do Hábito',
    'autor' => 'Charles Duhigg',
    'descricao' => 'Como os hábitos funcionam e como podemos transformá-los.',
    'ano' => 2012,
    'n_paginas' => 408,
    'id_genero' => 6
  ],
  [
    'titulo' => 'Mindset',
    'autor' => 'Carol S. Dweck',
    'descricao' => 'A psicologia do sucesso através da mentalidade de crescimento.',
    'ano' => 2006,
    'n_paginas' => 320,
    'id_genero' => 6
  ],
  [
    'titulo' => 'Inteligência Emocional',
    'autor' => 'Daniel Goleman',
    'descricao' => 'Por que a IE pode ser mais importante que o QI para o sucesso.',
    'ano' => 1995,
    'n_paginas' => 384,
    'id_genero' => 6
  ],
  [
    'titulo' => 'Os 7 Hábitos das Pessoas Altamente Eficazes',
    'autor' => 'Stephen Covey',
    'descricao' => 'Princípios fundamentais para eficácia pessoal e interpessoal.',
    'ano' => 1989,
    'n_paginas' => 432,
    'id_genero' => 6
  ],
  [
    'titulo' => 'Rápido e Devagar',
    'autor' => 'Daniel Kahneman',
    'descricao' => 'As duas formas de pensar e tomar decisões.',
    'ano' => 2011,
    'n_paginas' => 512,
    'id_genero' => 6
  ],

  // História (id_genero: 7)
  [
    'titulo' => 'Sapiens',
    'autor' => 'Yuval Noah Harari',
    'descricao' => 'Uma breve história da humanidade desde a idade da pedra até a era moderna.',
    'ano' => 2011,
    'n_paginas' => 464,
    'id_genero' => 7
  ],
  [
    'titulo' => '1808',
    'autor' => 'Laurentino Gomes',
    'descricao' => 'Como a família real portuguesa fugiu de Napoleão e mudou a história do Brasil.',
    'ano' => 2007,
    'n_paginas' => 414,
    'id_genero' => 7
  ],
  [
    'titulo' => 'Gulag',
    'autor' => 'Anne Applebaum',
    'descricao' => 'A história dos campos de trabalho soviéticos.',
    'ano' => 2003,
    'n_paginas' => 720,
    'id_genero' => 7
  ],
  [
    'titulo' => 'A Segunda Guerra Mundial',
    'autor' => 'Winston Churchill',
    'descricao' => 'As memórias do primeiro-ministro britânico sobre a guerra.',
    'ano' => 1948,
    'n_paginas' => 1056,
    'id_genero' => 7
  ],
  [
    'titulo' => 'Homo Deus',
    'autor' => 'Yuval Noah Harari',
    'descricao' => 'Uma breve história do amanhã e o futuro da humanidade.',
    'ano' => 2015,
    'n_paginas' => 464,
    'id_genero' => 7
  ],

  // Tecnologia (id_genero: 8)
  [
    'titulo' => 'Código Limpo',
    'autor' => 'Robert C. Martin',
    'descricao' => 'Habilidades práticas para escrever código de qualidade.',
    'ano' => 2008,
    'n_paginas' => 464,
    'id_genero' => 8
  ],
  [
    'titulo' => 'O Programador Pragmático',
    'autor' => 'Andrew Hunt e David Thomas',
    'descricao' => 'De aprendiz a mestre na arte da programação.',
    'ano' => 1999,
    'n_paginas' => 352,
    'id_genero' => 8
  ],
  [
    'titulo' => 'Algoritmos',
    'autor' => 'Thomas H. Cormen',
    'descricao' => 'Teoria e prática dos algoritmos computacionais.',
    'ano' => 1990,
    'n_paginas' => 1312,
    'id_genero' => 8
  ],
  [
    'titulo' => 'Inteligência Artificial',
    'autor' => 'Stuart Russell e Peter Norvig',
    'descricao' => 'Uma abordagem moderna sobre IA e suas aplicações.',
    'ano' => 1995,
    'n_paginas' => 1152,
    'id_genero' => 8
  ],
  [
    'titulo' => 'Design Patterns',
    'autor' => 'Erich Gamma',
    'descricao' => 'Elementos de software orientado a objetos reutilizável.',
    'ano' => 1994,
    'n_paginas' => 416,
    'id_genero' => 8
  ]
];

try {
  $conn->beginTransaction();

  $sql = "INSERT INTO categorias(descricao) VALUES (?);";
  $stmt = $conn->prepare($sql);
  foreach ($categorias as $categoria) {
    $stmt->execute($categoria);
  }

  $conn->commit();
} catch (PDOException $e) {
  if($conn->inTransaction()) {
    $conn->rollBack();
  }
 
  echo "Erro ao inserir os dados: " . $e->getMessage() . PHP_EOL;
  exit;
}
