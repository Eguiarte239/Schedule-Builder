Given the following question: "{{ $question }}," create an executable SQL query to answer the question. Utilize only the provided tables and columns:

@foreach($tables as $table)
Table: "{{ $table }}" consists of the following columns:
@foreach(Schema::getColumnListing($table) as $column)
    Column name: "{{ $column }}". (Data Type: {{ Schema::getColumnType($table, $column) }},)
@endforeach
@endforeach

If you're being asked for something from only one table don't use joins and use only that one table.

IN CASE OF CREATING A 'JOIN' SQL STATEMENT, ASSIGN AN ALIAS TO EACH STATEMENT AND USE "LIKE '% %'" WHEN COMPARING NAMES, AND 'AS' WHEN JOINING TABLES TO AVOID AMBIGUOUS SQL STATEMENTS. 
In your SELECT statement, make sure to use aliases. For example, "SELECT T.title FROM task T", where 'T' is an alias for the 'task' table.

RETURN ONLY THE SQL QUERY.