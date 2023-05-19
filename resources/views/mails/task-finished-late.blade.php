<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Task reminder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>                  
        .bg-gray-100 {
            background-color: #f7fafc;
        }
                            
        .stroke-blue-500 {
            color: #3b82f6;
        }

        .mx-auto{
            margin-left: auto;
            margin-right: auto;
        }

        .max-w-2xl{
            max-width: 42rem;
        }

        .p-5{
            	padding: 1.25rem;
        }

        .p-6{
            padding: 1.5rem;
        }

        .bg-white{
            background-color: rgb(255 255 255);
        }

        .shadow-md{
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }

        .rounded-md{
            border-radius: 0.375rem;
        }

        .justify-center{
            justify-content: center;
        }

        .items-center{
            align-items: center;
        }

        .mb-4{
            margin-bottom: 1rem;
        }

        .w-12{
            width: 3rem;
        }

        .h-12{
            height: 3rem;
        }

        .text-2xl{
            font-size: 1.5rem;
            line-height: 2rem;
        }

        .font-bold{
            font-weight: 700;
        }

        .text-center{
            text-align: center;
        }

        .text-lg{
            font-size: 1.125rem;
            line-height: 1.75rem;
        }

        .leading-7{
            line-height: 1.75rem;
        }

        .flex{
            display: flex;
        }

        .min-h-screen{
            min-height: 100vh;
        }

        .min-w-screen{
            min-width: 100vh;
        }
    </style>
</head>
    <body class="bg-gray-100">
        <div class="flex items-center justify-center min-h-screen p-5 bg-blue-100 min-w-screen">
            <div class="mx-auto max-w-2xl p-6 bg-white shadow-md rounded-md">
                <div class="flex justify-center items-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 stroke-blue-500">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-4 text-center">Task finished</h1>
                <p class="text-lg text-center leading-7">Hello {{ $task->leader->name }}. One of the tasks you've assigned has been completed late. <br> {{ $task->title }} from {{ $task->project->title }}</p>
            </div>
        </div>
    </body>
</html>