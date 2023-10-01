Given the following question: "{{ $question }}," create an executable SQL query to answer the question. Utilize only the following tables and columns:
If the question doesn't mention a table just return "[]"

@foreach($tables as $table)
Table: "{{ $table }}" consists of the following columns:
@foreach(Schema::getColumnListing($table) as $column)
    Column name: "{{ $column }}". (Data Type: {{ Schema::getColumnType($table, $column) }},)
@endforeach
@endforeach

AVOID USING 'JOINS' WHEN THE QUESTION IS ABOUT A SINGLE 'TABLE', AND KEEP SIMPLE THE SQL STATEMENT, IN CASE OF CREATING A 'JOIN' SQL STATEMENT, ASSIGN AN ALIAS TO EACH STATEMENT AND USE "LIKE '% %'" WHEN COMPARING NAMES, AND 'AS' WHEN JOINING TABLES TO AVOID AMBIGUOUS SQL STATEMENTS.
IF YOU DON'T USE JOINS MAKE SURE TO GET ALL THE TABLES YOU NEED, FOR EXAMPLE "SELECT P.title FROM projects P, users WHERE P.leader_id = users.id".
In your SELECT statement, make sure to use aliases. For example, "SELECT T.title FROM task T", where 'T' is an alias for the 'task' table. IF THE 'JOIN' HAPPENS USE THIS EXAMPLE TO GET MORE INFORMATION FROM THE TABLES
FOR EXAMPLE "SELECT P.title FROM projects P, WHERE P.title IN ('EXAMPLE','EXAMPLE 2');" INFORMATION CAN ONLY BE OBTAINED FROM A SINGLE TABLE IF MULTIPLE RECORDS ARE QUERIED.
IF THE QUESTION IS ABOUT ONE SINGLE TABLE, BUT YOU NEED INFORMATION FROM ANOTHER TABLE, YOU'RE ALLOWED TO USER A SINGLE JOIN, ITS IMPORTANT NOT DOING SUB-QUERIES, OR CONSULT MORE THAN ONE TABLE IN A SQL SENTENCE
IN A WHERE CLAUSE USE "leader_id" TO SEARCH INFORMATION


RETURN ONLY THE SQL QUERY IN ENGLISH,NOT A SENTENCE.
