# Parametros de filtros, ordenação e pesquisa

Cada parâmetro de consulta, exceto as funções pré-definidas _with, _sort, _q e _group_by é interpretado como um filtro.

Exemplo: Listar todas as escolas que começam com nome: Vieira.

<pre><code>/api/schools?name-lk=Vieira*</code></pre>

No exemplo acima o parametro name recebe o nome vieira que e o nome da escola, é com prefixo -lk sendo este igual a like do sql.

Todos os filtros podem ser combinados com um operador AND.

<pre><code>/api/schools?name-lk=Vieira*&created_at-min=2016-12-01 12:55:02</code></pre>

O exemplo acima resultaria na seguinte SQL:

<pre><code>Where `schools` LIKE "Vieira%" AND `created_at` >= "2016-12-01 12:55:02"</code></pre>

É também possível usar vários valores para um filtro.
Vários valores estão separados por barra vertical |.
Vários valores são combinados com OR a não ser quando há um -not como sufixo, então eles são combinados com AND.
Por exemplo, todos os livros com o ID de 5 ou 6:

<pre><code>/api/schools?id=5|6</code></pre>

Ou todas as escolas, exceto aqueles com ID 5 ou 6:

<pre><code>/api/schools?id-not=5|6</code></pre>

O mesmo pode ser conseguido usando o -in como sufixo:

<pre><code>/api/schools?id-in=5,6</code></pre>

Respectivamente, o not-insufixo:

<pre><code>/api/schools?id-not-in=5,6</code></pre>

| Suffix        | Operator  | Meaning                               |
|:--------------|-----------|---------------------------------------|
| `-lk`         | LIKE      | O mesmo que o operador LIKE do SQL    |
| `-not-lk`     | NOT LIKE  | O mesmo que o operador NOT LIKE do SQL|
| `-in`         | IN        | O mesmo que o operador IN do SQL      |
| `-not-in`     | NOT IN    | O mesmo que o operador NOT IN do SQL  |
| `-min`        | >=        | Maior ou igual a                      |
| `-max`        | <=        | Menor ou igual a                      |
| `-st`         | <         | Menor que                             |
| `-gt`         | >         | Maior que                             |
| `-not`        | !=        | Diferente                             |


# Parametros paginação

Toda action GET em qualquer coleção de dados contém os parametros padrões para paginação de resultados.
São eles: _page, _per_page.

<pre><code>GET /api/schools?_page=2&_per_page=50</code></pre>

Neste exemplo o resultado será a segunda página da coleção de resultados da listagem de escolas,
com 50 escolas por página.

Veja o exemplo:

{ 
    "per_page": "156",
    "current_page": "2",
    "last_page": "4",
    "next_page_url": "https://schools.logoseducacao.com.br/api/schools?_page=3"
    "prev_page_url": "https://schools.logoseducacao.com.br/api/schools?_page=1"
    "from": "50",
    "to": "100",
    "data": [
      {
          "id": 1, 
          "name": 
          "Name of school", 
          "type": "middle-school"
      },
      ....
}

# Parametros de transformação

Toda action GET em qualquer coleção de dados contém o parametro _group_by.
Esse parametro é utilizado para agrupar os itens do resultado por um atributo contido nesses registros.

Veja o exemplo:

<pre><code>GET /api/schools</code></pre>

Considerando que esse recurso retornaria o resultado


```
{ 
    "per_page": "15",
    "current_page": "1",
    "last_page": "3",
    "next_page_url": "https://schools.logoseducacao.com.br/api/schools?_page=2"
    "prev_page_url": "null"
    "from": "1",
    "to": "15",
    "data": [
      {
          "id": 1, 
          "name": 
          "Name of school", 
          "type": "middle-school"
      },
      {
          "id": 1, 
          "name": 
          "Name of school", 
          "type": "middle-school"
      },
      {
          "id": 1, 
          "name": 
          "Name of school", 
          "type": "high-school"
      },
    ]
}
```

<pre><code>GET /api/schools</code></pre>

Como o parametro _group_id=type, teriamos:

```
{ 
    "per_page": "15",
    "current_page": "1",
    "last_page": "3",
    "next_page_url": "https://schools.logoseducacao.com.br/api/schools?_page=2"
    "prev_page_url": "null"
    "from": "1",
    "to": "15",
    "data": [
       "middle-school": [
          {
              "id": 1, 
              "name": 
              "Name of school", 
              "type": "middle-school"
          },
          {
              "id": 1, 
              "name": 
              "Name of school", 
              "type": "middle-school"
          }
       ],
       "high-school": [
          {
              "id": 1, 
              "name": 
              "Name of school", 
              "type": "high-school"
          }
       ]
    ]
}
```