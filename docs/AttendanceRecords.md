# Group Attendance Records

`Attendance Records` são os registros de presenças dos alunos em uma aula, definindo
se o aluno estava ou não presente durante a aula.

## Attendance records Collection [/attendance-records{?_q,_sort,_with}]

### List attendance records [GET]

<!-- include(ParameterFilter.md) -->


+ Parameters
    + _q (string, optional) - Pesquisa por palavra-chave, a correspondência e incluida conjunto de resultados.
    + _sort (string, optional) - Ordena a coluna desejada, de forma acendente ou descendente.
    + _with (string, optional) - Obtem informações do recurso relacionado.
        + Members
            + lesson
            + lesson.schoolClass
            + lesson.schoolClass.grade
            + lesson.schoolClass.shift
            + lesson.subject
            + student.person
            + student.schoolClass

+ Request 
    + Headers

            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array)
            + (AttendanceRecord)


### Create a new attendance record [POST]

O registro de presença de um aluno em uma aula é único.
Se o registro de presença que já existe for solicitado, será
retornado o registro já existente com as informações atualizadas.

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (AttendanceRecordFillable)
            
+ Response 201 (application/json) 
    
    + Headers
            
            Location: /attendance_records/1
    
    + Attributes 
        + attendance_record (AttendanceRecord)

+ Response 409 (application/json)

        {
            "message": "The record of the student (student.id = 1 ) to the lesson already exists.",
            "status_code": 409
        }


## Attendance Record [/attendance-records/{attendance_record_id}{?_with}]

+ Parameters
    + attendance_record_id: 1 (number) - ID of the attendance record
    
### View a attendance record detail [GET]

+ Parameters
    + _with (string, optional) - Obtem informações do recurso relacionado.
        + Members
            + lesson
            + lesson.schoolClass
            + lesson.schoolClass.grade
            + lesson.schoolClass.shift
            + lesson.subject
            + student.person
            + student.schoolClass

+ Request 
    + Headers

            authorization: <!-- include(Token.md) -->

+ Response 200 (application/json)

    + Attributes 
        + attendance_record (AttendanceRecord)

### Edit [PUT]

+ Parameters
    + attendance_record_id: 1 (number) - ID of the attendance record
    
+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (AttendanceRecordFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + attendance_record (AttendanceRecord)
