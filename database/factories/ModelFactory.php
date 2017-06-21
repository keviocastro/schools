<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$faker = Faker\Factory::create('pt_BR');
$factory->define(App\SchoolCalendar::class, function() use ($factory, $faker) {
    
    $dateTime = $faker->dateTimeThisDecade;
    
    return [
        'year' => $dateTime->format('Y'),
        'start' => $dateTime->setDate($dateTime->format('Y'), 
            rand(1,3), rand(1,28))->format('Y-m-d'),
        'end' => $dateTime->setDate($dateTime->format('Y'), 
            rand(11,12), rand(1,30))->format('Y-m-d'),
        'average_formula' => '({1 Bim} + {2 Bim} + {3 Bim} + {4 Bim})/4',
    ];
});

$factory->define(App\School::class, function () use ($factory, $faker) {
    return [
        'name' => $faker->company.' school',
        'contacts' => [
            [
                "name" => "Central de atendimento ao aluno", 
                "phone" => $faker->phoneNumber,
                "email" => $faker->email
            ],
            [
                "name" => "Diretoria", 
                "phone" => $faker->phoneNumber,
                "email" => $faker->email
            ]
        ],
    ];
});


$factory->define(App\SchoolClass::class, function ($faker, $attributes) use ($factory) {
    
    if (empty($attributes['evaluation_type'])) {
        $evaluation_type = $faker->randomElement(App\EvaluationTypeRepository::all());
    }else{
        $evaluation_type = $attributes['evaluation_type'];
    }

    return [
        'identifier' => $faker->randomLetter(),
        'shift_id' => function(){
        	return factory(App\Shift::class)->create()->id;
        },
        'grade_id' => function(){
        	return factory(App\Grade::class)->create()->id;
        },
        'school_id' => function(){
            return factory(App\School::class)->create()->id;
        },
        'school_calendar_id' => function(){
            return factory(App\SchoolCalendar::class)->create()->id;
        },
        'evaluation_type' => $evaluation_type, 
        'progress_sheet_id' => function() use ($evaluation_type){
            if ($evaluation_type == App\EvaluationTypeRepository::PROGRESS_SHEET_PHASE) {
                return factory(App\ProgressSheet::class)->create()->id;
            }
        },

    ];
});

$factory->define(App\Shift::class, function () use ($factory, $faker) {
    return [
        'name' => $faker->randomElement(['matutino','noturno','vespertino']),
    ];
});

$factory->define(App\Subject::class, function () use ($factory, $faker) {
    return [
        'name' => $faker->randomElement([
            'Matématica',
            'Ciências',
            'Inglês',
            'História',
            'Língua Portuguesa',
            'Espanhol',
            'Francês',
            'Educação Física',
            'Teologia',
            'Física',
            'Física Quântica',
            'Filosofia',
            'Direito',
            'Artes',
            'Química',
            'Química',
            'Estátistica',
            'Computação',
            'Geometria',
            'Astronomia',
            'Horologia',
            'Biologia',
            'Botânica',
            'Ecologia',
            'Genética',
            'Neurociência',
            'Nutrição',
            'Zoologia',
            ]),
    ];
});

$factory->define(App\Grade::class, function () use ($factory, $faker) {
    return [
        'name' => $faker->randomElement([
        		'Jardim I',
        		'Jardim II',
        		'1º Série',
        		'2º Série',
        		'3º Série',
        		'4º Ano',
        		'5º Ano',
        		'6º Ano',
        		'7º Ano',
        		'8º Ano',
        		'9º Ano',
        		'1º Ano',
        		'2º Ano',
        		'3º Ano',
        	]),
    ];
});


$factory->define(App\Person::class, function () use ($factory, $faker) {
    
    $gender = $faker->randomElement(['women' ,'men']);
    $avatarUrl = 'https://randomuser.me/api/portraits/'.$gender.'/'.rand(1,80).'.jpg';
    $gender = ($gender == 'women') ? 'female' : 'male';
    
    return [
    	'name' => $faker->name($gender), 
    	'birthday' => $faker->dateTimeThisCentury->format('Y-m-d'), 
    	'gender' => $gender, 
    	'place_of_birth' => $faker->city, 
    	'more' => $faker->text(),
        'avatarUrl' => $avatarUrl,
        'phone' => $faker->phoneNumber,
        'user_id' => $faker->uuid, // user_id do serviço de autentificação: exemplo auth0.
    	];
});

$factory->define(App\Student::class, function ($faker) use ($factory) {
    
    return [
	    	'person_id' => function(){
	    		return 	factory(App\Person::class)->create()->id;
	    	},
    	];
});

$factory->define(App\StudentResponsible::class, function ($faker) use ($factory) {
    
    return [
            'student_id' => function(){
                return  factory(App\Student::class)->create()->id;
            },
            'person_id' => function(){
                return factory(App\Person::class)->create()->id;
            },
        ];
});


$factory->define(App\Lesson::class, function ($faker) use ($factory) {
    
    $start = $faker->dateTimeThisYear();
    return [
            'school_class_id' => function(){
                return  factory(App\SchoolClass::class)->create()->id;
            },
            'teacher_id' => function(){
                return factory(App\Teacher::class)->create()->id;
            },
            'subject_id' => function(){
                return factory(App\Subject::class)->create()->id;
            },
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $start->modify('+ 30 minutes')->format('Y-m-d H:i:s'),
            'lesson_plan_id' => function(){
                return factory(App\LessonPlan::class)->create()->id;
            }
        ];
});

$factory->define(App\Lesson::class, function ($faker) use ($factory) {
    
    $start = $faker->dateTimeThisMonth('+ 30 days');
    return [
            'school_class_id' => function(){
                return  factory(App\SchoolClass::class)->create()->id;
            },
            'subject_id' => function(){
                return factory(App\Subject::class)->create()->id;
            },
            'start' => $start->format('Y-m-d H:i:s'),
            'end' => $start->modify('+ 30 minutes')->format('Y-m-d H:i:s'),
        ];
}, 'Next15Days');

$factory->define(App\SchoolClassStudent::class, function ($faker) use ($factory) {
    
    return [
            'school_class_id' => function(){
                return  factory(App\SchoolClass::class)->create()->id;
            },
            'student_id' => function(){
                return factory(App\Student::class)->create()->id;
            },
        ];
});

$factory->define(App\AttendanceRecord::class, function ($faker, $attributes) use ($factory) {
    if(empty($attributes['presence'])){
        $presence = rand(0,2);
    }
    
    $absenceDismissal = '';
    if ($presence == 2) {
        $absenceDismissal = $faker->sentence;
    }
    return [
            'lesson_id' => function(){
                return  factory(App\Lesson::class)->create()->id;
            },
            'student_id' => function(){
                return factory(App\Student::class)->create()->id;
            },
            'presence' => $presence,
            'absence_dismissal' => $absenceDismissal,
        ];
});

$factory->define(App\SchoolCalendarPhase::class, function ($faker, $attributes) use ($factory) {
    
    if (empty($attributes['school_calendar_id'])) {
        $schoolCalendar = factory(App\SchoolCalendar::class)->create();
    }else{
        $schoolCalendar = App\SchoolCalendar::findOrFail($attributes['school_calendar_id']);
    }

    $startDate = $faker->dateTimeBetween($schoolCalendar->start, $schoolCalendar->end);
    $endDate = $faker->dateTimeBetween($startDate, $schoolCalendar->end);

    return [
            'school_calendar_id' => $schoolCalendar->id,
            'name' => $faker->randomElement([
                    '1º Bimestre',
                    '2º Bimestre',
                    '3º Bimestre',
                    '4º Bimestre',
                    '1º Semestre',
                    '2º Semestre',
                    'N1',
                    'N2',
                    'Reavaliação 1',
                    'Reavaliação 2',
                    'Recuperação 1',
                    'Recuperação 2',
                ]),
            'start' => $startDate,
            'end' => $endDate,
            'average_formula' => 'arithmetical',
        ];
});

$factory->define(App\Assessment::class, function ($faker) use ($factory) {
    
    return [
            'school_calendar_phase_id' => function(){
                return  factory(App\SchoolCalendarPhase::class)->create()->id;
            },
            'name' => $faker->randomElement([
                    'Nota 1',
                    'Nota 2',
                    'Nota 3',
                    'Prova 1',
                    'Prova 2',
                    'Prova 3',
                    'Avaliação 1',
                    'Avaliação 2',
                    'Avaliação 3',
                    'N1',
                    'N2',
                    'N3',
                    'N1.1',
                    'N2.1',
                    'N3.1',
                ]),
        ];
});

$factory->define(App\Level::class, function ($faker) use ($factory) {
    
    return [
        'name' => $faker->randomElement(['leve', 'medio', 'grave']),
    ];
});

$factory->define(App\Occurence::class, function ($faker) use ($factory) {
    
    return [
        'level_id' => function(){
            return factory(App\Level::class)->create()->id;
        },
        'comment' => $faker->sentence,
        'about_person_id' => function(){
            return factory(App\Student::class)->create()->id;
        },
    ];
});

$factory->define(App\StudentGrade::class, function ($faker, $attributes) use ($factory) {

    return [
        'grade' => $faker->randomFloat(1,0,10),
        'student_id' => function(){
            return factory(App\Student::class)->create()->id;
        },
        'subject_id' => function(){
            return factory(App\Subject::class)->create()->id;
        },
        'assessment_id' =>  function(){
            return factory(App\Assessment::class)->create()->id;
        },
        'school_class_id' => function(){
            return factory(App\SchoolClass::class)->create()->id;
        },
    ];
});

$factory->define(App\Teacher::class, function ($faker) use ($factory) {
    
    return [
        'person_id' => function(){
            return factory(App\Person::class)->create()->id;
        }
    ];
});

$factory->define(App\LessonPlan::class, function ($faker) use ($factory) {

    $lessonPlanModel = factory(App\LessonPlanModel::class)->create()->toArray();
    
    foreach ($lessonPlanModel['definition'] as $key => $item) {
        $lessonPlanModel['definition'][$key]['value'] = $faker->realText(rand(20,150));
    }
    
    return [
        'lesson_plan_template_id' => $lessonPlanModel['id'],
        'content' => $lessonPlanModel['definition']
    ];
});

$factory->define(App\LessonPlanModel::class, function ($faker) use ($factory) {
 
    $models = [
        [
            ['name' => 'Tema da aula', 'type' => 'text', 'required' => true],
            ['name' => 'Objetivos Gerais', 'type' => 'long-text', 'required' => true],
            ['name' => 'Objetivos Específicos', 'type' => 'long-text', 'required' => true],
            ['name' => 'Problematização', 'type' => 'long-text', 'required' => true],
            ['name' => 'Metodologia', 'type' => 'long-text', 'required' => true],
            ['name' => 'Avaliação', 'type' => 'long-text', 'required' => true],
            ['name' => 'Cronograma', 'type' => 'long-text', 'required' => false],
        ],
        [
            ['name' => 'Objetivos', 'type' => 'long-text', 'required' => true],
            ['name' => 'Conteudo', 'type' => 'long-text', 'required' => true],
            ['name' => 'Metodo ou estrategia', 'type' => 'long-text', 'required' => true],
            ['name' => 'Recursos didaticos', 'type' => 'long-text', 'required' => false],
            ['name' => 'Avaliacao', 'type' => 'long-text', 'required' => true],
        ],
        [
            ['name' => 'Tema da aula', 'type' => 'text', 'required' => true],
            ['name' => 'Objetivos', 'type' => 'long-text', 'required' => true],
            ['name' => 'Conteudo', 'type' => 'long-text', 'required' => true],
            ['name' => 'Metodo ou estrategia', 'type' => 'long-text', 'required' => true],
            ['name' => 'Avaliacao', 'type' => 'long-text', 'required' => true],
        ]
    ];

    return [
        'definition' => $faker->randomElement($models)
    ];
});

$factory->define(App\ProgressSheet::class, function ($faker) use ($factory) {
    $evalution = [
        'Ficha de acompanhamento ',
        'Ficha de desempenho ',
        'Ficha de ensino ',
        'Ficha avaliativa '
    ];
    $for = [
        'adultos',
        'infantil'          
    ];

    $options = [
        [
            ["identifier" => "I", "label" => "Irregular"],
            ["identifier" => "R", "label" => "Regular"],
            ["identifier" => "B", "label" => "Bom"],
            ["identifier" => "O", "label" => "Ótimo"],
        ],
        [
            ["identifier" => "B", "label" => "Bom"],
            ["identifier" => "R", "label" => "Ruim"],
        ],
        [
            ["identifier" => "C", "label" => "Completo"],
            ["identifier" => "P", "label" => "Parcial"],
            ["identifier" => "I", "label" => "Incompleto"],
        ]
    ];

    return [
        'name' => $faker->randomElement($evalution).$faker->randomElement($for),
        'options' =>  $faker->randomElement($options)
    ];
});

$factory->define(App\Group::class, function ($faker) use ($factory) {
    return [
        'name' => $faker->randomElement([
                'ANÁLISE LINGUÍSTICA: APROPRIAÇÃO DO SISTEMA  DE ESCRITA ALFABÉTICA',
                'LEITURA',
                'PRODUÇÃO DE TEXTOS ESCRITOS',
                'ORALIDADE',
                'ANÁLISE LINGUÍSTICA: DISCURSIVIDADE, TEXTUALIDADE E NORMATIVIDADE.',
                'NÚMEROS E OPERAÇÕES',
                'GEOMETRIA',
                'GRANDEZAS E MEDIDAS',
                'TRATAMENTO DE INFORMAÇÕES',
                'PNAIC',
            ]),
        'order' => rand(1,10)
    ];
});

$factory->define(App\ProgressSheetItem::class, function ($faker) use ($factory) {

    return [
        'progress_sheet_id' => function(){
            return factory(App\ProgressSheet::class)->create()->id;
        },
        'group_id' => function(){
            return factory(App\Group::class)->create()->id;
        },
        'name' => $faker->randomElement([
            'Escreve o próprio nome.',
            'Reconhece e nomeia as letras do alfabeto.',
            'Diferencia letras de números e outros símbolos.',
            'Conhece a ordem alfabética e seus usos em diferentes gêneros.',
            'Reconhece diferentes tipos de letras em textos de diferentes gêneros e suportes textuais.',
            'Usa diferentes tipos de letras em situações de escrita de palavras e textos.',
            'Percebe que palavras diferentes variam quanto ao número, repertório e ordem de letras.',
            'Segmenta oralmente as sílabas de palavras e compara as palavras quanto ao tamanho.',
            'Identifica semelhanças sonoras em sílabas e em rimas.',
            'Reconhece que as sílabas variam quanto às suas composições.',
            'Percebe que as vogais estão presentes em todas as sílabas.',
            'Lê, ajustando a pauta sonora ao escrito.',
            'Domina as correspondências entre letras ou grupos de letras e seu valor sonoro, de modo a ler palavras e textos.',
            'Domina as correspondências entre letras ou grupos de letras e seu valor sonoro, de modo a escrever palavras e textos.',
            'Lê textos não verbais, em diferentes suportes.',
            'Lê textos (poemas, canções, tirinhas, textos de tradição oral, dentre outros), com autonomia.',
            'Compreende textos lidos por outras pessoas de diferentes gêneros e com diferentes propósitos.',
            'Reconhece finalidades de textos lidos pelo professor ou pelas crianças.',
            'Localiza informações explícitas em textos de diferentes gêneros, temáticas, lidos pelo professor ou outro leitor experiente.',
            'Realiza inferências em textos de diferentes gêneros e temáticas, lidos com autonomia.',
            'Compara comprimento de dois ou mais objetos por comparação direta (sem o uso de unidades de medidas convencionais) para identificar: maior, menor, igual, mais alto, mais baixo, mais comprido, mais curto, mais grosso, mais fino, mais largo etc.',
            'Identifica ordem de eventos em programações diárias, usando palavras como: antes, depois.',
            'Identifica  unidades de tempo — dia, semana, mês, bimestre, semestre, ano — e utilizar calendários.',
            'Lê horas, comparando relógios digitais e de ponteiros.',
            'Reconhece cédulas e moedas que circulam no Brasil e de possíveis trocas entre cédulas e moedas em função de seus valores em experiências com dinheiro em brincadeiras ou em situações de interesse das crianças',
        ]),
    ];
});
$factory->define(App\StudentProgressSheet::class, function ($faker, $attributes) use ($factory) {

    // Regras para preparar dado para que o registro seja para ao estudante
    // matriculado na turma do qual o item de avaliação está relacionado.
    if (empty($attributes['progress_sheet_item_id'])) {
        $progressSheetItem = factory(App\ProgressSheetItem::class)->create();
    }else{
        $progressSheetItem = App\ProgressSheetItem::findOrFail($attributes['progress_sheet_item_id']);
    }

    if (empty($attributes['school_class_id'])) {
        $schoolClass = factory(App\SchoolClass::class)->create();
    }else{
        $schoolClass = App\SchoolClass::findOrFail($attributes['school_class_id']);
    }

    $phases = $schoolClass->schoolCalendar->phases->toArray();
    if (empty($phases)) {
        $phases = factory(App\SchoolCalendarPhase::class, 4)->create()->toArray();
    }
    $phase = $faker->randomElement($phases); 

    $options = $progressSheetItem->progressSheet->options;

     // identifier também pode ser null
     // define que o registro foi iniciado mas não foi escolhido uma opção
    array_push($options, ['identifier' => null]);
    $option = $faker->randomElement($options);

    if (empty($attributes['student_id'])) {
        $student = factory(App\Student::class)->create();
        factory(App\SchoolClassStudent::class)->create([
                'student_id' => $student->id,
                'school_class_id' => $schoolClass->id
            ]);
    }else{
        $student = App\Student::findOrFail($attributes['student_id']);
    }

    return [
        'option_identifier' => $option['identifier'],
        'progress_sheet_item_id' => $progressSheetItem->id,
        'student_id' => $student->id,
        'school_calendar_phase_id' => $phase['id'],
        'school_class_id' => $schoolClass->id
    ];
});
