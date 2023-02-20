<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Info Cerdas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <style type="text/css">
   
    .container {
      width: auto;
      max-width: 680px;
      padding: 0 15px;
    }
    </style>
  </head>
  <body>
     <?php 
      $info = DB::table('info_cerdas')->where('uuid', $uuid)->first();
     ?>
     <main class="flex-shrink-0">
      <div class="container">
        <h1 class="mt-5"></h1>
        <div class="card">
          <img src="{{url('gambar/'.$info->gambar)}}" class="card-img-top">
          <div class="card-body">
            <h5 class="card-title">{{$info->judul}}</h5>
            <p class="card-text">
              {!! $info->isi !!}
            </p>
          </div>
      </div>
      </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pprn3073KE6tl6bjs2QrFaJGz5/SUsLqktiwsUTF55Jfv3qYSDhgCecCxMW52nD2" crossorigin="anonymous"></script>
  </body>
</html>