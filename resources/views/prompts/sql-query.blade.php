Given the following question: "{{ $question }}," create an executable SQL query to answer the question. Utilize only the following tables and columns:

@foreach($tables as $table)
Table: "{{ $table }}" consists of the following columns:
@foreach(Schema::getColumnListing($table) as $column)
    Column name: "{{ $column }}". (Data Type: {{ Schema::getColumnType($table, $column) }},)
@endforeach
@endforeach

AVOID USING UNNECESSARY 'JOINS' WHEN THE ASK ITS ABOUT A SINGLE 'TABLE', AND KEEP SIMPLE THE SQL STATEMENT, IN CASE OF CREATING A 'JOIN' SQL STATEMENT, ASSIGN AN ALIAS TO EACH STATEMENT AND USE "LIKE '% %'" WHEN COMPARING NAMES, AND 'AS' WHEN JOINING TABLES TO AVOID AMBIGUOUS SQL STATEMENTS. 
In your SELECT statement, make sure to use aliases. For example, "SELECT T.title FROM task T", where 'T' is an alias for the 'task' table. IF THE 'JOIN' HAPPENS USE THE EXAMPLE TO USE MORE INFORMATION FROM THE TABLES
FOR EXAMPLE "SELECT P.title FROM projects P, WHERE P.title IN ('EXAMPLE','EXAMPLE 2');" INFORMATION CAN ONLY BE OBTAINED FROM A SINGLE TABLE IF MULTIPLE RECORDS ARE QUERIED.
WHEN YOU ARE GIVEN A QUESTION WITH "AND" or "Y" IN IT, CONSIDER MAKING MORE THAN ONE SQL QUERY, RETURN IT AS A LIST, DO NOT USE /n. EXAMPLE: "query 1", "query 2"
WHEN RETURNING ONE SINGLE SQL QUERY RETURN IT ALSO IN QUOTES SEPARATED BY A COMA.
RETURN ONLY THE SQL QUERY. NOT A SENTENCE.