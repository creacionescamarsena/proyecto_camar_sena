<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Creaciones Camar</title>
<link rel="shortcut icon" href="{{ asset('img/logo.png') }}">

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
background:#f8f8f8;
color:#222;
}

/* NAVBAR */

header{
background:white;
padding:20px 8%;
display:flex;
justify-content:space-between;
align-items:center;
box-shadow:0 2px 10px rgba(0,0,0,.08);
position:sticky;
top:0;
z-index:1000;
}

.logo{
font-family:'Cormorant Garamond',serif;
font-size:42px;
font-weight:700;
color:#4D6D2E;
letter-spacing:1px;
}

nav{
display:flex;
gap:35px;
}

nav a{
text-decoration:none;
color:#333;
font-weight:500;
transition:.3s;
}

nav a:hover{
color:#4D6D2E;
}

.btn-login{
background:#4D6D2E;
color:white;
padding:12px 25px;
border-radius:10px;
text-decoration:none;
font-weight:600;
}

/* HERO */

.hero{
min-height:90vh;
background:linear-gradient(rgba(0,0,0,.35),rgba(0,0,0,.35)),
url('https://images.unsplash.com/photo-1520975954732-35dd22299614?q=80&w=1200');
background-size:cover;
background-position:center;
display:flex;
justify-content:center;
align-items:center;
text-align:center;
color:white;
padding:20px;
}

.hero-content{
max-width:900px;
}

.hero span{
background:#6F8F4E;
padding:10px 20px;
border-radius:50px;
}

.hero h1{
font-family:'Cormorant Garamond',serif;
font-size:95px;
font-weight:600;
line-height:.9;
margin:25px 0;
}

.hero h1 em{
font-style:italic;
font-weight:500;
}

.hero p{
font-size:22px;
margin-bottom:35px;
}

.hero-buttons{
display:flex;
justify-content:center;
gap:15px;
flex-wrap:wrap;
}

.btn-primary{
background:#4D6D2E;
color:white;
padding:15px 30px;
border-radius:10px;
text-decoration:none;
}

.btn-secondary{
background:white;
color:#4D6D2E;
padding:15px 30px;
border-radius:10px;
text-decoration:none;
}

/* ESTADISTICAS */

.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
padding:60px 8%;
background:white;
}

.stat{
text-align:center;
}

.stat h2{
font-size:40px;
color:#4D6D2E;
}

/* PRODUCTOS */

.productos{
padding:80px 8%;
}

.titulo{
text-align:center;
margin-bottom:50px;
}

.titulo h2{
font-family:'Cormorant Garamond',serif;
font-size:60px;
font-weight:600;
}

.cards{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:30px;
}

.card{
background:white;
border-radius:20px;
overflow:hidden;
box-shadow:0 5px 15px rgba(0,0,0,.08);
transition:.3s;
}

.card:hover{
transform:translateY(-10px);
}

.card img{
width:100%;
height:300px;
object-fit:cover;
}

.card-body{
padding:20px;
}

.btn-vermas{
    display:block;
    margin-top:15px;
    width:100%;
    text-align:center;
    padding:12px;
    background:#4D6D2E;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:500;
}

.categoria{
background:#4D6D2E;
color:white;
padding:5px 15px;
border-radius:30px;
display:inline-block;
font-size:14px;
}

.card h3{
margin:15px 0;
}

.precio{
font-size:24px;
font-weight:700;
color:#4D6D2E;
}

.card button{
margin-top:15px;
width:100%;
padding:12px;
border:none;
background:#4D6D2E;
color:white;
border-radius:10px;
cursor:pointer;
}

/* NOSOTROS */

.nosotros{
padding:80px 8%;
background:white;
text-align:center;
}

.nosotros h2{
font-family:'Cormorant Garamond',serif;
font-size:60px;
font-weight:600;
margin-bottom:20px;
}

.nosotros p{
max-width:800px;
margin:auto;
font-size:18px;
line-height:1.8;
}

/* FOOTER */

footer{
background:#1f1f1f;
color:white;
padding:50px;
text-align:center;
}

@media(max-width:768px){

header{
flex-direction:column;
gap:15px;
}

.hero h1{
font-size:45px;
}

.stats{
grid-template-columns:repeat(2,1fr);
}

}

</style>
</head>
<body>

<header>

<div class="logo">
Creaciones Camar
</div>

<nav>
<a href="#">Inicio</a>
<a href="#productos">Productos</a>
<a href="#nosotros">Nosotros</a>
<a href="#contacto">Contacto</a>
</nav>

<a href="{{ route('login') }}" class="btn-login">
Iniciar Sesión
</a>

</header>

<section class="hero">

<div class="hero-content">

<span>NUEVA COLECCIÓN 2026</span>

<h1>
Colección de <br>
<em>chaquetas</em>
</h1>

<p>
Diseños exclusivos, materiales premium y la mejor calidad para cualquier ocasión.
</p>

<div class="hero-buttons">

<a href="#productos" class="btn-primary">
Ver Catálogo
</a>

<a href="{{ route('login') }}" class="btn-secondary">
Comprar Ahora
</a>

</div>

</div>

</section>

<section class="stats">

<div class="stat">
    <h2>✓</h2>
    <p>Calidad Garantizada</p>
</div>

<div class="stat">
    <h2>★</h2>
    <p>Atención Personalizada</p>
</div>

<div class="stat">
    <h2>🎨</h2>
    <p>Diseños Exclusivos</p>
</div>

<div class="stat">
    <h2>🤝</h2>
    <p>Compromiso y Confianza</p>
</div>

</section>

<section class="productos" id="productos">

<div class="titulo">
<h2>Productos Destacados</h2>
</div>

<div class="cards">

<div class="card">
    <img src="https://images.unsplash.com/photo-1523398002811-999ca8dec234?q=80&w=800">

    <div class="card-body">
        <span class="categoria">Deportiva</span>
        <h3>Chaqueta Urban</h3>
        <p class="precio">$180.000</p>

        <a href="{{ route('login') }}" class="btn-vermas">
            Ver Más
        </a>
    </div>
</div>
<div class="card">
<img src="https://i.blogs.es/4580f2/chaqueta-beisbolera/450_1000.jpeg">

<div class="card-body">
<span class="categoria">Premium</span>
<h3>Chaqueta Beisbolera</h3>
<p class="precio">$200.000</p>
<a href="{{ route('login') }}" class="btn-vermas">
            Ver Más
        </a>
</div>
</div>

<div class="card">
<img src="https://images.pexels.com/photos/6311392/pexels-photo-6311392.jpeg">
<div class="card-body">
<span class="categoria">Casual</span>
<h3>Chaqueta Casual</h3>
<p class="precio">$100.000</p>
<a href="{{ route('login') }}" class="btn-vermas">
            Ver Más
        </a>
</div>
</div>

</div>

</section>

<section class="nosotros" id="nosotros">

<h2>¿Por qué elegirnos?</h2>

<p>
En Creaciones Camar diseñamos y fabricamos chaquetas con materiales de alta calidad,
combinando elegancia, comodidad y durabilidad para ofrecer productos únicos que
reflejan la personalidad de cada cliente.
</p>

</section>

<footer id="contacto">

<h3>Creaciones Camar</h3>

<p>Moda • Calidad • Estilo</p>

<p>• creacionescamarsena@gmail.com</p>

</footer>

</body>
</html>