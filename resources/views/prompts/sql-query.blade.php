Given the following question: {{ $question }}, make a valid SQL query to answer the question.
Only use the following tables and columns:

@foreach($tables as $table)
Table: "{{ $table->getName() }}" has columns:
@foreach(Schema::getColumnListing($table->getName()) as $column)
    Column name: "{{ $column }}". (Data Type: {{ Schema::getColumnType($table->getName(), $column) }},)
@endforeach
@endforeach
