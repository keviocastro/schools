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


$factory->define(App\SchoolClass::class, function () use ($factory, $faker) {
    $evaluation_type = $faker->randomElement(App\EvaluationTypeRepository::all());

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

$factory->define(App\AttendanceRecord::class, function ($faker) use ($factory) {
    $presence = rand(0,2);
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
        'assessment_id' => function(){
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
            ["identifier" => "I", "label" =>"Incompleto"],
        ],
    ];

    $options = json_encode(
        $faker->randomElement($options)
    );

    return [
        'name' => $faker->randomElement($evalution).$faker->randomElement($for),
        'options' => $options
    ];
});