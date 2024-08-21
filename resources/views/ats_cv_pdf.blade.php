<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <style>
        /* Add your custom CSS styles here */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        h1,
        h2,
        h3 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 10px;
        }

        hr {
            border: 1px solid #ccc;
            margin: 20px 0;
        }

        .personal-info {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="personal-info">
        <h1>{{ $user->first_name }} {{ $user->last_name }}</h1>
        <p>{{ $user->user_cv->job_field }}</p>
        <p>
    {{ $user->email }} | {{ $user->phone }}
    @if(isset($user->links[0]))
        | {{ $user->links[0]->link }}
    @else
        | No links
    @endif
</p>
    </div>
    <!-- <hr> -->
    <!-- <section>
        <h2>Summary</h2>
        <p>Experienced software engineer with a strong background in full-stack development and a passion for creating
            innovative and efficient solutions. Adept at collaborating with cross-functional teams, problem-solving, and
            delivering high-quality projects within deadline.</p>
    </section> -->
    <hr>
    <section>
        <h2>Education</h2>
        <ul>
            <li>
                <h5>
    @if(isset($user->education))
        {{ $user->education }}
    @else
         No Education
    @endif
                </h5>
            </li>
        </ul>
    </section>
    <hr>
    <section>
        <h2>Work Experience</h2>
        <ul>
        @forelse ($user->experiences as $experience)
            <li>
                <h3>{{$experience->name}}</h3>
                <p>{{$experience->start_date}} - {{$experience->end_date}}</p>
                <ul>
                    <li>{{$experience->details}}</li>
                    <li>Company name: {{$experience->company_name}}</li>
                    <li>Job title: {{$experience->job_title}}</li>
                </ul>
            </li>
            @empty
            <li>No experiences</li>
        @endforelse
            <li>
    </section>
    <hr>
    <section>
        <h2>References</h2>
        <ul>
            @forelse ($user->references as $reference)
            <li>
                <h3>{{$reference->name}}</h3>
                <ul>
                    <li>Employment: {{$reference->employment}}</li>
                    <li>Email: {{$reference->email}}</li>
                    <li>Phone: {{$reference->phone}}</li>
                </ul>
            </li>
            @empty
            <li>No experiences</li>
        @endforelse
            <li>
    </section>
    <hr>
    <section>
        <h2>Certifications</h2>
        <ul>
        @forelse ($user->cvCourses as $course)
            <li>
                <h3>{{$course['name']}}</h3>
                <p>Source: {{$course['source']}}</p>
                <p>Duration: {{$course['duration']}}</p>
                <p>{{$course['details']}}</p>
            </li>
            @empty
            <li>No courses</li>
       @endforelse
        </ul>
    </section>
    <hr>
    <section>
        <h2>Skills</h2>
        <ul>
            <li>{{$user->user_cv->skills}}</li>
        </ul>
    </section>
</body>

</html>