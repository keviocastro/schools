# Group Student Grades

`Student Grades` São registo de notas de cada aluno em suas diciplinas nos periodos e turmas pertencentes a ele.

## Student grades Collection [/student_grades{?limit,offset,q,sort,field,_with
}]

### Create a new Student Grades [POST]

+ Request (application/json)
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
    + Attributes (StudentGradeFillable)

+ Response 201 (application/json) 
    
    + Attributes 
        + StudentGrade (StudentGrade)
        
### List Student Grades [GET]

+ Parameters
    + q (string, optional) - Fulltext search.
    + sort (string, optional) - Nome da coluna para ordenação. 
    + _with
 (string) - Nome da relação a ser incluída na resposta.
        + Members
            + student
            + subject
            + assessment
            + schoolClass

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
+ Response 200 (application/json)

    + Attributes 
        + Include Paginator
        + data (array)
            + (StudentGrade_with
Relation)

## Student Grades [/student-grades/{id}{?_with
}]

### View a Student Grades detail [GET]

+ Parameters
    + id: 1 (number) - ID of the occurence
    + _with
 (string) - Nome da relação a ser incluída na resposta.
        + Members
            + student
            + subject
            + assessment
            + schoolClass

+ Request 
    + Headers
            
            authorization: <!-- include(Token.md) -->
    
+ Response 200 (application/json)
    
    + Attributes 
        + StudentGrade_with
Relation (StudentGrade_with
Relation)

### Edit [PUT]

+ Parameters
    + id: 1 (number) - ID of the Student Grades

+ Request (application/json)

    + Headers
            
            authorization: <!-- include(Token.md) -->
            
    + Attributes (StudentGradeFillable)
            
+ Response 200 (application/json)
    
    + Attributes 
        + StudentGrade (StudentGrade)
