Given the following question: '{{ $question }},' create an executable SQL query to answer the question. Utilize only the following tables and columns:

@foreach($tables as $table)
Table: '{{ $table }}' consists of the following columns:
@foreach(Schema::getColumnListing($table) as $column)
    Column name: '{{ $column }}'. (Data Type: {{ Schema::getColumnType($table, $column) }},)
@endforeach
@endforeach

If the question contains something that has nothing to do with the tables provided, please just return an empty array like "[]". In this case don't create a sql query.
An example of the above would be the following: "How many projects are there and what is your favorite color" or "How many phases are there? and what is your favorite animal?". Notice that in these two questions there is something not related to projects, phases, tasks or users. 
In this case you must return an empty array like the one I mentioned before.
Otherwire you can continue with the following instructions:
Table 'projects' has the following foreign keys:
1. 'user_id' which references the table 'users' in its column 'id' (this field refers to the user that created the project).
2. 'leader_id' which references the table 'users' in its column 'id' (this field refers to the user in charge of the project, it means it's the project leader).
Table 'phases' has the following foreign keys:
1. 'user_id' which references the table 'users' in its column 'id' (this field refers to the user that created the phase).
2. 'project_id' which references the table 'projects' in its column 'id' (this field refers that this phase belongs to the project with that id).
Table 'tasks' has the following foreign keys:
1. 'user_id' which references the table 'users' in its column 'id' (this field refers to the user that created the task).
2. 'project_id' which references the table 'projects' in its column 'id' (this field refers that this task belongs to the project with that id).
3. 'phase_id' which references the table 'phases' in its column 'id' (this field refers that this task belongs to the phase with that id).
4. 'user_id_assigned' which references the table 'users' in its column 'id' (this field refers that this task is being assigned to the user with that id to its completion).
Another important field contained in the tables 'projects', 'phases', 'tasks' is the column 'priority' which can only have one of the following values: 'Low','Medium','High','Urgent'

When using 'projects' table, do not use the user_id column, instead use the leader_id column when necessary.
AVOID USING 'JOINS' WHEN THE QUESTION IS ABOUT A SINGLE 'TABLE', AND KEEP SIMPLE THE SQL STATEMENT, IN CASE OF CREATING A 'JOIN' SQL STATEMENT, ASSIGN AN ALIAS TO EACH STATEMENT AND USE "LIKE '% %'" WHEN COMPARING NAMES, AND 'AS' WHEN JOINING TABLES TO AVOID AMBIGUOUS SQL STATEMENTS.
IF YOU DON'T USE JOINS MAKE SURE TO GET ALL THE TABLES YOU NEED, FOR EXAMPLE "SELECT P.title FROM projects P, users WHERE P.leader_id = users.id".
In your SELECT statement, make sure to use aliases. For example, "SELECT T.title FROM task T", where 'T' is an alias for the 'task' table. IF THE 'JOIN' HAPPENS USE THIS EXAMPLE TO GET MORE INFORMATION FROM THE TABLES
FOR EXAMPLE "SELECT P.title FROM projects P, WHERE P.title IN ('EXAMPLE','EXAMPLE 2');" INFORMATION CAN ONLY BE OBTAINED FROM A SINGLE TABLE IF MULTIPLE RECORDS ARE QUERIED.
IF THE QUESTION IS ABOUT ONE SINGLE TABLE, BUT YOU NEED INFORMATION FROM ANOTHER TABLE, YOU'RE ALLOWED TO USER A SINGLE JOIN, ITS IMPORTANT NOT DOING SUB-QUERIES, OR CONSULT MORE THAN ONE TABLE IN A SQL SENTENCE
IN A WHERE CLAUSE USE "leader_id" TO SEARCH INFORMATION
Only answer questions related to projects, tasks and phases. Don't include specific string values in the SQL queries.
Make the SQL query as simple as possible. When asking for general information from one table, instead of using 'SELECT *', please select all columns excluding the columns related to ids from tables.
Return only the SQL query but remember, if the question has something that involves projects, phases, tasks or users then return just an empty array.
