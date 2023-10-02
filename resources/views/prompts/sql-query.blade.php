Given the following question: '{{ $question }},' create an executable SQL query to answer the question. Utilize only the following tables and columns:

@foreach($tables as $table)
Table: '{{ $table }}' consists of the following columns:
@foreach(Schema::getColumnListing($table) as $column)
    Column name: '{{ $column }}'. (Data Type: {{ Schema::getColumnType($table, $column) }},)
@endforeach
@endforeach

Table 'projects' has the following foreign keys:
1. 'user_id' which references the table 'users' in its column 'id' (this field refers to the user that created the project).
2. 'leader_id' which references the table 'users' in its column 'id' (this field refers to the user in charge of the project).
Table 'phases' has the following foreign keys:
1. 'user_id' which references the table 'users' in its column 'id' (this field refers to the user that created the phase).
2. 'project_id' which references the table 'projects' in its column 'id' (this field refers that this phase belongs to the project with that id).
Table 'tasks' has the following foreign keys:
1. 'user_id' which references the table 'users' in its column 'id' (this field refers to the user that created the task).
2. 'project_id' which references the table 'projects' in its column 'id' (this field refers that this task belongs to the project with that id).
3. 'phase_id' which references the table 'phases' in its column 'id' (this field refers that this task belongs to the phase with that id).
4. 'user_id_assigned' which references the table 'users' in its column 'id' (this field refers that this task is being assigned to the user with that id).
Another important field contained in the tables 'projects', 'phases', 'tasks' is the column 'priority' which can only have one of the following values: 'Low','Medium','High','Urgent'

WHEN using 'projects' table, do not use the user_id column, instead use the leader_id column when necessary.
ONLY ANSWER QUESTION RELATED TO projects, tasks and phases. DON'T INCLUDE SPECIFIC STRING VALUES IN THE SQL QUERIES.
MAKE THE SQL QUERY AS SIMPLE AS POSSIBLE. WHEN ASK FOR GENERAL INFORMATION FROM ONE TABLE, INSTEAD OF USING 'SELECT *', PLEASE SELECT THE COLUMNS EXCLUDING THE COLUMNS RELATED TO ids FROM TABLES.
RETURN ONLY THE SQL QUERY.