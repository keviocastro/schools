# Group Students

Ações relacionadas a informações dos alunos: cadastrais, indicadores de desempenho
e relatórios como "Boletim anual do aluno" e "Histórico escolar".

## Annual Report [/students/{student_id}/annual-report{?school_calendar_id}]

### Annual Report [GET]

+ Parameters
    + school_calendar_id: 1 (required, number) - ID do ano letivo

+ Request 
    + Headers
    
            authorization: <!-- include(Token.md) -->
            
+ Response 200 (application/json)

    + Attributes (object)
        + averages (array) - Médias por disciplina no ano letivo
            + (object)
                + include (SchoolCalendarPhase)
                + subject_average (array)
                    + (object)
                        + include (Subject)
                        + average: 7.2 - Média do aluno para a disciplina na fase do ano
                        + student_grades (array)
                            + (object)
                                + student_grade (StudentGrade)
                                + assessment (Assessment)
        + absences (array) - Faltas no ano letivo
            + (object)
                + school_calendar_phase_id: 1
                + subject_id: 1 
                + absences: 6
        + subjects (array) - `Disciplinas cursadas no ano`
            + include (Subject)
            + average: 8.2 - `Nota média da disciplina no ano`
            + average_calculation: `((9.6 + 9.3)*0.4 + (9.3 + 9.8)*0.6)/2` - `Calculo de média da disciplina`


## Annual Student Summary [/students/{student_id}/annual-summary{?school_calendar_id}]

### View a annual student summary [GET]

+ Parameters
    + school_calendar_id: 1 (number, required) - `ID do ano letivo`

+ Request 
    + Headers
    
            authorization: <!-- include(Token.md) -->
            
+ Response 200 (application/json)

    + Attributes (object)
        + absences (object)
            + total: 22 (number) - Total de faltas do aluno no ano letivo
        + best_average (object) - Melhor média do aluno no ano
            + include (Subject)
            + average: 9.5 (number) - Média
            + average_calculation: `(7.8 + 8.5)/2` (string) - Calculo da média
            + student_grades (array) - Notas do aluno que compõem a média 
                + (object)
                    + include (StudentGrade)
                    + assessment (Assessment) 
        + low_average (object) - Pior média do aluno no ano
            + include (Subject)
            + average (number)
            + average_calculation (string)
            + student_grades (array) 
                + (object)
                    + include (StudentGrade)
                    + assessment (Assessment) 
