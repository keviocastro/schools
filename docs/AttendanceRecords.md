# Group Attendance Records

`Attendance Records` são os registros de presenças dos alunos em uma aula, definindo
se o aluno estava ou não presente durante a aula.

## Attendance records Collection [/attendance-records{?q,sort,_with
}]

### List attendance records [GET]

+ Parameters
    + q (string, optional) - Fulltext search
    + sort (string, optional) - Nome da coluna para ordenação. 
    + _with
 (string) - Nome da relação a ser incluída na resposta
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


## Attendance Record [/attendance-records/{id}{?_with
}]

+ Parameters
    + id: 1 (number) - ID of the attendance record
    

### View a attendance record detail [GET]

+ Parameters
    + _with
 (string) - Include related models
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

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (AttendanceRecordFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + attendance_record (AttendanceRecord)
