# Parametros de filtros em listagem

Cada parâmetro de consulta, exceto as funções pré-definidas _with, _sort e _q, é interpretado como um filtro.

Exemplo: Listar todas as escola que começam com nome: Vieira.

<pre><code>/api/schools?name-lk=Vieira*</code></pre>

No exemplo acima o parametro name recebe o nome vieira que e o nome da escola, é com prefixo -lk sendo este igual a like do sql.

Todos os filtros podem ser combinados com um operador AND.

<pre><code>/api/schools?name-lk=Vieira*&created_at-min=2016-12-01 12:55:02</code></pre>

O exemplo acima resultaria na seguinte SQL:

<pre><code>Where `schools` LIKE "Vieira%" AND `created_at` >= "2016-12-01 12:55:02"</code></pre>

É também possível usar vários valores para um filtro. Vários valores estão separados por um tubo |. Vários valores são combinados com OR a não ser quando há um -not como sufixo, então eles são combinados com AND. Por exemplo, todos os livros com o ID de 5 ou 6:

<pre><code>/api/schools?id=5|6</code></pre>

Ou todos os livros, exceto aqueles com ID 5 ou 6:

<pre><code>/api/schools?id-not=5|6</code></pre>

O mesmo pode ser conseguido usando o -in como sufixo:

<pre><code>/api/schools?id-in=5,6</code></pre>

Respectivamente, o not-insufixo:

<pre><code>/api/schools?id-not-in=5,6</code></pre>

<table><thead>
	<tr>
		<th>Suffix</th>
		<th>Operator</th>
		<th>Meaning</th>
	</tr>
</thead><tbody>
<tr>
	<td>-lk</td>
	<td>LIKE</td>
	<td>O mesmo que o operador <code>LIKE</code> do SQL</td>
</tr>
<tr>
	<td>-not-lk</td>
	<td>NOT LIKE</td>
	<td>O mesmo que o operador <code>NOT LIKE</code> do SQL</td>
</tr>
<tr>
	<td>-in</td>
	<td>IN</td>
	<td>O mesmo que o operador <code>IN</code> do SQL</td>
</tr>
<tr>
	<td>-not-in</td>
	<td>NOT IN</td>
	<td>O mesmo que o operador <code>NOT IN</code> do SQL</td>
</tr>
<tr>
	<td>-min</td>
	<td>&gt;=</td>
	<td>Maior ou igual a</td>
</tr>
<tr>
	<td>-max</td>
	<td>&lt;=</td>
	<td>Menor ou igual a</td>
</tr>
<tr>
	<td>-st</td>
	<td>&lt;</td>
	<td>Menor que</td>
</tr>
<tr>
	<td>-gt</td>
	<td>&gt;</td>
	<td>Maior que</td>
</tr>
<tr>
	<td>-not</td>
	<td>!=</td>
	<td>Diferente</td>
</tr>
</tbody></table>