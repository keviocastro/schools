# Group Lessons

`Lessons` são as aulas programadas para as turmas de uma escola.

## Lessons Collection [/lessons{?q,sort,_with
}]

### List lessons [GET]

+ Parameters
    + _fields (string, optional)
        + include (Lesson)
    + q (string, optional) - Fulltext search
    + sort (string, optional) - Nome da coluna para ordenação. 
    + _with
 (string, optional) Nome da relação a ser incluida da resposta.
        + Members
            + schoolClass
            + schoolClass.grade
            + schoolClass.shift
            + subject

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array)
            + (Lesson)

### Create a new lesson [POST]

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (LessonFillable)
            
+ Response 200 (application/json) 
    
    + Attributes 
        + lesson (Lesson)


## Lesson [/lessons/{lesson_id}{?_with,attach}]

+ Parameters
    - lesson_id: 1 (number) - ID of the lesson

### View a lesson detail [GET]

+ Parameters
    + _with (string, optional) Nome da relação a ser incluida na resposta.
        + Members
            + attendanceRecords
            + schoolClass
            + schoolClass.grade
            + schoolClass.students
            + schoolClass.shift
            + schoolClass.students.responsibles
            + schoolClass.students.attendanceRecords
            + schoolClass.students.person
    + attach (string, optional) 
        
        Anexa mais informações da aula na responsta. 
        students.last_occurences são as ultimas 2 ocorrencias do aluno. 
        students.attendanceRecord é o registro de chamada da aula para o aluno. 
        students.absenceSummary é um resumo de faltas do aluno, durante todo
        o ano.

        + Members
            + students
            + students.attendanceRecord
            + students.last_occurences
            + students.absenceSummary

+ Request 
    + Headers

            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + lesson 
            + Include (Lesson)
            + students (array)
                + (object)
                    + Include (Student)
                    + attendance_record (AttendanceRecord)
                    + last_occurences (array[Occurence])
                    + absence_summary (object)
                        + percentage_absences_reprove: 25 - Percentual máximo de faltas as aulas para não ser           reprovado       
                        + total_lessons_year: 200 - total de aulas programadas no ano da mesma disciplina           
                        + total_absences_year: 22 - total de faltas que o aluno tem no ano da mesma disciplina
            
### Edit [PUT]

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (LessonFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + lesson (Lesson)


### Delete [DELETE]

+ Response 204


## Lessons per day [/lessons/per-day{?_with
,q,sort,start,end}]

### List Lessons per day [GET]

+ Parameters
    + _with
 (string, optional) Nome da relação a ser incluida da resposta.
        + Members
            + schoolClass
            + schoolClass.grade
            + schoolClass.shift
            + schoolClass.students
            + schoolClass.students.studentResponsible
            + schoolClass.students.attendanceRecords
            + subject
    + start (string, optional) Data inicial 
        + Default: NOW()
    + end (string, optional) Data final
        + Default: NOW() + 15 days 
    + q (string, optional) - Fulltext search

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + start: '2016-10-01' (string)
        + end: '2016-10-15' (string)
        + data (array)
            + (object)
                + day: `2016-10-01` (string)
                + lessons (array[Lesson])

