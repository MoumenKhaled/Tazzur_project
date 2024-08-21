<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Develop a single page HTML resume using Bootstrap 5</title>   
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
  </head>
  <body>
  <header class="bg-primary bg-gradient text-white py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-3 text-left text-md-center mb-3">
            <img class="rounded-circle img-fluid" src="https://i.pravatar.cc/175?img=32" alt="Profile Photo" />
          </div>
          <div class="col-md-9">
            <h1>{{ $user->first_name }} {{ $user->last_name }}</h1>
            <h5>{{ $user->user_cv->job_field }}</h5>
            <ul>
              <li><strong>Governorate: </strong> {{ $user->governorate }}</li>
              <li><strong>Gender: </strong> {{ $user->gender }}</li>
              <li><strong>Marital Status: </strong> {{ $user->marital_status }}</li>
              <li><strong>Birthday: </strong> {{ $user->birthday }}</li>
              <li><strong>Nationality: </strong> {{ $user->nationality }}</li>
              <li><strong>Experience Years: </strong> {{ $user->experience_years }}</li>
              <li><strong>Education: </strong> {{ $user->education }}</li>
              <li><strong>Topics: </strong>
                <?php
                  $topics = implode(', ', ($user->topic));
                  echo $topics;
                ?>
              </li>
              <li><strong>Driving License:</strong>
                <?php
                  if ($user->driving_license) {
                    echo 'Yes';
                  } else {
                    echo 'No';
                  }
                ?>
              </li>
              <li><strong>Military Status:</strong> {{ $user->military_status }}</li>
            </ul>
          </div>       
        </div>        
      </div>
    </header>
    <nav class="bg-dark text-white-50 mb-5">
      <div class="container">
          <div class="row p-3">
              <div class="col-md pb-2 pb-md-0">
                  <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-envelope" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2zm13 2.383l-4.758 2.855L15 11.114v-5.73zm-.034 6.878L9.271 8.82 8 9.583 6.728 8.82l-5.694 3.44A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.739zM1 11.114l4.758-2.876L1 5.383v5.73z"/>
                  </svg>
                  <a href="#" class="text-white ml-2">{{ $user->email }}</a>
              </div>
              <div class="col-md text-md-center pb-2 pb-md-0">
                  <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-globe" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm7.5-6.923c-.67.204-1.335.82-1.887 1.855A7.97 7.97 0 0 0 5.145 4H7.5V1.077zM4.09 4H2.255a7.025 7.025 0 0 1 3.072-2.472 6.7 6.7 0 0 0-.597.933c-.247.464-.462.98-.64 1.539zm-.582 3.5h-2.49c.062-.89.291-1.733.656-2.5H3.82a13.652 13.652 0 0 0-.312 2.5zM4.847 5H7.5v2.5H4.51A12.5 12.5 0 0 1 4.846 5zM8.5 5v2.5h2.99a12.495 12.495 0 0 0-.337-2.5H8.5zM4.51 8.5H7.5V11H4.847a12.5 12.5 0 0 1-.338-2.5zm3.99 0V11h2.653c.187-.765.306-1.608.338-2.5H8.5zM5.145 12H7.5v2.923c-.67-.204-1.335-.82-1.887-1.855A7.97 7.97 0 0 1 5.145 12zm.182 2.472a6.696 6.696 0 0 1-.597-.933A9.268 9.268 0 0 1 4.09 12H2.255a7.024 7.024 0 0 0 3.072 2.472zM3.82 11H1.674a6.958 6.958 0 0 1-.656-2.5h2.49c.03.877.138 1.718.312 2.5zm6.853 3.472A7.024 7.024 0 0 0 13.745 12H11.91a9.27 9.27 0 0 1-.64 1.539 6.688 6.688 0 0 1-.597.933zM8.5 12h2.355a7.967 7.967 0 0 1-.468 1.068c-.552 1.035-1.218 1.65-1.887 1.855V12zm3.68-1h2.146c.365-.767.594-1.61.656-2.5h-2.49a13.65 13.65 0 0 1-.312 2.5zm2.802-3.5h-2.49A13.65 13.65 0 0 0 12.18 5h2.146c.365.767.594 1.61.656 2.5zM11.27 2.461c.247.464.462.98.64 1.539h1.835a7.024 7.024 0 0 0-3.072-2.472c.218.284.418.598.597.933zM10.855 4H8.5V1.077c.67.204 1.335.82 1.887 1.855.173.324.33.682.468 1.068z"/>
                  </svg>
                  <a href="#" class="text-white ml-2">
                  {{$user->links[0]->link}}
                  </a>
              </div>       
              <div class="col-md text-md-right">
                  <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-telephone-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M2.267.98a1.636 1.636 0 0 1 2.448.152l1.681 2.162c.309.396.418.913.296 1.4l-.513 2.053a.636.636 0 0 0 .167.604L8.65 9.654a.636.636 0 0 0 .604.167l2.052-.513a1.636 1.636 0 0 1 1.401.296l2.162 1.681c.777.604.849 1.753.153 2.448l-.97.97c-.693.693-1.73.998-2.697.658a17.47 17.47 0 0 1-6.571-4.144A17.47 17.47 0 0 1 .639 4.646c-.34-.967-.035-2.004.658-2.698l.97-.969z"/>
                  </svg>
                  <a href="#" class="text-white ml-2">{{ $user->phone }}</a>
              </div>       
          </div>
      </div>
    </nav>
    <main class="container">
      <div class="row">
        <div class="col-md mb-5">
          <h2 class="mb-5">Experience</h2>
          <ul>
        @forelse ($user->experiences as $experience)
        <li>
        <h6 class="text-primary">{{$experience->name}} / {{$experience->start_date}} - {{$experience->end_date}}</h6>
        <p>{{$experience->details}}</p>
        <p>- Company name: {{$experience->company_name}}</p>
        <p>- Job title: {{$experience->job_title}}</p>
        </li>
        @empty
        <li>No experiences</li>
        @endforelse
          </ul>
        </div>
        <div class="col-md mb-5">
          <h2 class="mb-5">references</h2> 
          <ul>
          @forelse ($user->references as $reference)
    <li>
        <h6 class="text-primary">{{$reference->name}}</h6>
        <p>- Employment: {{$reference->employment}}</p>
        <p>- Email: {{$reference->email}}</p>
        <p>- Phone: {{$reference->phone}}</p>
    </li>
        @empty
        <li>No references </li>
        @endforelse
          </ul> 
        </div>     
      </div>    
      <div class="row">
        <div class="col-md mb-5">
          <h3>Information : </h3>     
          <div >
            <p>Work city: 
            @forelse (json_decode($user->user_cv->work_city) as $index => $city)
            @if ($index > 0)
            ,
             @endif
              {{$city}}
              @empty
               No cities .
              @endforelse
          </p>
          </div>       
          <div >
          <p>Current job: {{$user->user_cv->job_current}}</p>
          </div> 
          <div >
          <p>Languages: 
          @forelse (json_decode($user->user_cv->languages) as $index => $language)
          @if ($index > 0)
          ,
          @endif
          {{$language}}
          @empty
          No languages.
          @endforelse
          </p>
          </div>       
          <div >
          <p>Job environment: 
          @forelse (json_decode($user->user_cv->job_environment) as $index => $environment)
          @if ($index > 0)
          ,
         @endif
        {{$environment}}
        @empty
        No job environments.
        @endforelse
          </p>
          </div>       
          <div >
          <p>Job time: 
          @forelse (json_decode($user->user_cv->job_time) as $index => $jobtime)
          @if ($index > 0)
          ,
          @endif
         {{$jobtime}}
          @empty
          No job times .
          @endforelse
          </p>
          </div>
          <div >
           <p>Skills: {{$user->user_cv->skills}}</p>
          </div> 
          <div  >
            <div class="progress-bar bg-primary text-left pl-2" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
          </div>             
        </div>
        <div >
          <h2>Training Courses: </h2>
        <div>
        <ul>
        @forelse ($user->cvCourses as $course)
        <li>
        <div style="display: flex; align-items: center;">
        <div style="flex: 1;">
        
            <h6 class="text-primary">{{$course['name']}}</h6>
            <p>Source: {{$course['source']}}</p>
            <p>Duration: {{$course['duration']}}</p>
            <p>{{$course['details']}}</p>
        </div>
        <div style="flex: 1;">
           <!-- <img src="{{$course['image']}}" alt="Course Image" style="max-width: 100%;"> -->
        </div>
       </div>
       @empty
       No courses found.
       </li>
       @endforelse
       </ul> 
</div>
</div>
</div>
    </main>
    <footer class="bg-dark text-white-50 text-center mt-5 p-3">
      &copy; Tazzur 
  </body>
</html>