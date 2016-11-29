# Data structure

## StudentGradeFillable

+ grade: 7.5 (number) - Nota do aluno na avaliação.
+ student_id: 1 (number) - ID do aluno.
+ subject_id: 1 (number) - ID da disciplina relacionada a turma a qual o aluno realizou a avaliação.
+ assessment_id: 1 (number) - ID da avaliação.
+ school_class_id: 1 (number) - ID da turma onde a prova foi aplicada.

## StudentGrade

+ id: 1 (number) - Identificador único da nota do aluno.
+ Include (StudentGradeFillable)

## OccurenceFillable (object)

+ comment: fulano fez isso (string) - descrição sobre os fatos da occorencia
+ level_id: 1 (number) - Nivel da occorencia
+ about_person_id: 2 (number) - ID do usuario que esta recebendo a occorencia

## Occurence (object)

+ id: 1 (number) - Identificador único da ocorrência.
+ Include (OccurenceFillable)

## Level 

+ id: 1 (number)
+ name: Grave (string)

## Assessment (object)

+ id: 1 (number) - Identificador único da avaliação
+ name: Nota N1 - Descrição da avaliação
+ school_calendar_phase_id: 1 (number) - ID da fase que a avaliação será registrada.

## SchoolCalendarPhase

+ id: 1 (number) - Identificador único da fase avaliativa do ano.
+ name: `1º Bimestre` (string)
+ start: `2016-02-01` (string) - Data inicial do período avaliativo
+ end: `2016-04-01` (string) - Ultimo dia do período avaliativo
+ school_calendar_id: 1 - ID do ano letivo a qual esta fase está relacionada.

## SchoolCalendar (object)

+ id: 1 (number) - Identificador único do ano letivo.
+ include (SchoolCalendarFillable)

## SchoolCalendarFillable (object)

+ year: 2014 (number) - Ano de referência
+ start: `2016-02-01` - Inicio do ano letivo
+ end: `2016-12-10` - Fim do ano letivo
+ average_formula: `( ({1º Bimestre} + {2º Bimestre})*0.4 + ({3º Bimestre} + {4º Bimestre})*0.6 )/2` - `Fórmula utilizada para calcular médias do aluno no ano` 

## SchoolFillable (object)

+ name: Escola luz do saber (string) - Nome da escola

## School (SchoolFillable)

+ id: 1 (number) - Identificador único da escola

## SchoolClassBase (object)

+ identifier: A (string) - Abreviação/identificação da turma. Exmplo: B, para segunda turma do 1º ano (de 1 Ano B).

## SchoolClassFillable (SchoolClass)

+ grade_id: 1 (number) - ID do ano estudantíl.
+ shift_id: 1 (number) - ID do turno.
+ school_calendar_id: 1 (number) - ID do Ano letivo em que a turma existe.
+ school_id: 1 (number) - ID da escola relacionada.

## SchoolClass (SchoolClassBase)

+ id: 1 (number) - Identificador único da turma.
+ grade_id: 1 (number) - Ano estudantíl (Jardim I/1º Ano/...) da turma.
+ shift_id: 1 (number) - Turno (vespertino/matutino/...) da turma.
+ school_calendar_id: 1 (number) - ID do ano letivo

## SchoolClassSubject

+ id: 1 (number) - Identificador único da disciplina em uma turma.
+ school_class_id - ID da turma
+ subject_id - ID da disciplina

## Grade (object)

+ id: 1 (number) - Identificador único de um ano estudantíl. 
+ name: 3 Ano (string) - Nome do ano estudantíl. Exemplos: 2 Ano, 3 Ano, Jardim I, Jardim II.

## Shift (object)

+ id: 1 (number) - Identificador único do turno.
+ name: Matutino (string) - Nome do turno. Exemplo: Vespertino, matutino, noturno.


## Subject (object)

+ id: 1 (number) - Identificador único da disciplina.
+ name: Matématica  (string) - Nome da disciplina. Ex.: Matématica, Português, Fisica.

## LessonFillable (object)

+ school_class_id: 1 (number) - `Identificador único da turma.`
+ subject_id: 1 (number) - `Identificador único da disciplina (Matématica, física, ...).`
+ start: `2016-08-30 13:17:11` (string) - `Data e horário de inicio da aula.`
+ end: `2016-08-30 13:17:11` (string) - `Data e horário de termino da aula.`
+ teacher_id: 1 (number) - `ID do professor.`

## Lesson (object)

+ id: 1 (number) - `Identificador único  da aula.`
+ include (LessonFillable)

## Teacher (object)

+ id: 1 (number) - `Identificador único do professor.`
+ person_id: 1 (number) - `ID das informações básicas de pessoa do professor (nome, sobrenome, etc.)`

## Person (object)

+ id: 1 (number) - Identificador único da pessoa
+ name: Caio Fernando Chaves (string) - Nome
+ age: 15 (number) - Idade
+ birthday: `2001-04-21` (string) - Data de aniversário
+ piture: `https://randomuser.me/api/portraits/lego/6.jpg` (string) - Foto
+ gender: male (string)
+ place_of_birth: Santa Alenssandra (string) - Cidade de nascimento
+ more: descricao (string) - Mais detalhes sobre a pessoa
+ phone: `(62) 8385-5421` (string) - numero de telefone da pessoa

## Student (object)

+ id: 1 (number) - Identificador único do estudante.
+ person_id: 1 (number) - ID das informações básicas de pessoa do aluno (nome, sobrenome, etc.).

## AttendanceRecordFillable (object)

+ lesson_id: 1 (number) - Identificador único da aula relacionada
+ student_id: 1 (number) - Identificador único do aluno
+ presence: 1 (number) - Presença do aluno na aula
                         0 Faltou a aula
                         1 Estava presente
                         2 Falta abonada

## AttendanceRecord (object)

+ id: 1 (number) - Identificador único do registro de presença
+ Include AttendanceRecordFillable

## Paginator (object)

+ total: 25 (number) - Total de resultados encontrados
+ per_page: 15 (number) - Quantidade de resultados por página
+ current_page: 1 (number) - Página atual
+ last_page: 2 (number) - Ultima página
+ next_page_url: `http://localhost/api/<resource>?page=2` (string, nullable) - Url para resultados da proxima página
+ prev_page_url: null (string, nullable) - Url para resultados da página anterior
+ from: 1 (number) - Número inicial de resultados de paginação da página atual
+ to: 15 (number) - Número final de resultados de paginação da página atual