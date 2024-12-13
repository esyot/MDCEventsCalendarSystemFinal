<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>List of Events</title>
    <style>
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }

      
        th {
            text-align: center;
            font-weight: 500;
            background-color: #79a6ff; /* Tailwind bg-gray-500 */
            color: white;
            padding: 10px;
            font-size: 1rem;
            border-bottom: 2px solid #333;
        }

       
        td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

      
        tr:nth-child(even) {
            background-color: #e8e8e8; /* Light gray background for even rows */
        }

        tr:hover {
            background-color: #e2e8f0;
        }
        #title{
            margin-top: 60px;
            font-size: 25px;
        }
        #container{
            display: flex;
            flex-direction: row;
            width: 100%;
            text-align: center;
            line-height: -1.5;
        }
        #container img{
            width: 60px;
        }
    </style>
</head>
<body>
    <div id="container">
        <div>
            <img src="../public/assets/logo/logo.png" alt="">
            <h2>Mater Dei College </h2>
            <small>Cabulijan, Tubigon, Bohol</small>

        </div>
      
        <h1 id="title">{{$title}}</h1>
    </div>


    <table>
        <thead>
            <tr>
                <th>Event Name</th>
                <th>Venue</th>
                <th>Department</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Event Coordinator</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->event_name }}</td>
                    <td>{{ $event->venue_name }} at {{ $event->venue_building }}</td>
                    <td>{{ $event->department_acronyms }}</td>
                    <td>{{ Carbon\Carbon::parse($event->date_start)->format('D, F j, Y')}}</td>
                    <td>{{ Carbon\Carbon::parse($event->date_end )->format('D, F j, Y')}}</td>
                    <td>{{ ($event->user_fname)   }} {{ $event->user_lname }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
