Given the following question: {{ $question }}, make a executable SQL query to answer the question.
Only use the following tables and columns:

@foreach($tables as $table)
Table: "{{ $table}}" has columns:
@foreach(Schema::getColumnListing($table) as $column)
    Column name: "{{ $column }}". (Data Type: {{ Schema::getColumnType($table, $column) }},)
@endforeach
@endforeach

Return only the SQL Query.