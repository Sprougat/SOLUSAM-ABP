<!DOCTYPE html> <!-- Menentukan tipe dokumen HTML5 -->
<html lang="en"> <!-- Bahasa dokumen diatur ke English -->
<head>
    <meta charset="UTF-8"> <!-- Set karakter encoding ke UTF-8 -->
    <title>Welcome to CodeIgniter 4!</title> <!-- Judul halaman yang muncul di tab browser -->
    <meta name="description" content="The small framework with powerful features"> <!-- Deskripsi halaman untuk SEO -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Mengatur responsive untuk mobile -->
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"> <!-- Favicon browser -->

    <!-- STYLES -->
    <style {csp-style-nonce}> <!-- Style internal dengan CSP nonce untuk keamanan -->
        * {
            transition: background-color 300ms ease, color 300ms ease; /* Animasi transisi untuk background dan color */
        }
        *:focus {
            background-color: rgba(221, 72, 20, .2); /* Warna saat elemen fokus */
            outline: none; /* Menghilangkan garis fokus default */
        }
        html, body {
            color: rgba(33, 37, 41, 1); /* Warna teks default */
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji"; /* Font default */
            font-size: 16px; /* Ukuran font default */
            margin: 0; /* Menghapus margin default */
            padding: 0; /* Menghapus padding default */
            -webkit-font-smoothing: antialiased; /* Rendering font halus di Webkit */
            -moz-osx-font-smoothing: grayscale; /* Rendering font halus di OSX */
            text-rendering: optimizeLegibility; /* Optimalkan keterbacaan teks */
        }
        header {
            background-color: rgba(247, 248, 249, 1); /* Warna background header */
            padding: .4rem 0 0; /* Padding atas dan bawah */
        }
        .menu {
            padding: .4rem 2rem; /* Padding menu */
        }
        header ul {
            border-bottom: 1px solid rgba(242, 242, 242, 1); /* Border bawah */
            list-style-type: none; /* Hilangkan bullet list */
            margin: 0; /* Hilangkan margin default */
            overflow: hidden; /* Menyembunyikan overflow */
            padding: 0; /* Hilangkan padding */
            text-align: right; /* Rata kanan */
        }
        header li {
            display: inline-block; /* Membuat list item sejajar horizontal */
        }
        header li a {
            border-radius: 5px; /* Membuat sudut membulat */
            color: rgba(0, 0, 0, .5); /* Warna teks link */
            display: block; /* Membuat link blok */
            height: 44px; /* Tinggi link */
            text-decoration: none; /* Hilangkan underline */
        }
        header li.menu-item a {
            border-radius: 5px; /* Sudut membulat */
            margin: 5px 0; /* Margin vertikal */
            height: 38px; /* Tinggi link */
            line-height: 36px; /* Line height untuk vertikal align */
            padding: .4rem .65rem; /* Padding internal */
            text-align: center; /* Teks rata tengah */
        }
        header li.menu-item a:hover,
        header li.menu-item a:focus {
            background-color: rgba(221, 72, 20, .2); /* Warna background saat hover/focus */
            color: rgba(221, 72, 20, 1); /* Warna teks saat hover/focus */
        }
        header .logo {
            float: left; /* Mengapungkan logo ke kiri */
            height: 44px; /* Tinggi logo */
            padding: .4rem .5rem; /* Padding logo */
        }
        header .menu-toggle {
            display: none; /* Hide menu toggle default */
            float: right; /* Mengapungkan ke kanan */
            font-size: 2rem; /* Ukuran font toggle */
            font-weight: bold; /* Tebal font toggle */
        }
        header .menu-toggle button {
            background-color: rgba(221, 72, 20, .6); /* Warna background tombol */
            border: none; /* Hilangkan border */
            border-radius: 3px; /* Sudut membulat */
            color: rgba(255, 255, 255, 1); /* Warna teks tombol */
            cursor: pointer; /* Pointer saat hover */
            font: inherit; /* Menginherit font dari parent */
            font-size: 1.3rem; /* Ukuran font tombol */
            height: 36px; /* Tinggi tombol */
            padding: 0; /* Padding internal */
            margin: 11px 0; /* Margin vertikal */
            overflow: visible; /* Overflow visible */
            width: 40px; /* Lebar tombol */
        }
        header .menu-toggle button:hover,
        header .menu-toggle button:focus {
            background-color: rgba(221, 72, 20, .8); /* Warna saat hover/focus */
            color: rgba(255, 255, 255, .8); /* Warna teks saat hover/focus */
        }
        header .heroe {
            margin: 0 auto; /* Center horizontal */
            max-width: 1100px; /* Maks lebar */
            padding: 1rem 1.75rem 1.75rem 1.75rem; /* Padding internal */
        }
        header .heroe h1 {
            font-size: 2.5rem; /* Ukuran font H1 */
            font-weight: 500; /* Ketebalan font */
        }
        header .heroe h2 {
            font-size: 1.5rem; /* Ukuran font H2 */
            font-weight: 300; /* Ketebalan font */
        }
        section {
            margin: 0 auto; /* Center section */
            max-width: 1100px; /* Maks lebar */
            padding: 2.5rem 1.75rem 3.5rem 1.75rem; /* Padding section */
        }
        section h1 {
            margin-bottom: 2.5rem; /* Margin bawah H1 */
        }
        section h2 {
            font-size: 120%; /* Ukuran font H2 */
            line-height: 2.5rem; /* Line height */
            padding-top: 1.5rem; /* Padding atas */
        }
        section pre {
            background-color: rgba(247, 248, 249, 1); /* Warna background pre */
            border: 1px solid rgba(242, 242, 242, 1); /* Border pre */
            display: block; /* Display block */
            font-size: .9rem; /* Ukuran font */
            margin: 2rem 0; /* Margin vertikal */
            padding: 1rem 1.5rem; /* Padding */
            white-space: pre-wrap; /* Wrap text */
            word-break: break-all; /* Break word */
        }
        section code {
            display: block; /* Code tampil block */
        }
        section a {
            color: rgba(221, 72, 20, 1); /* Warna link */
        }
        section svg {
            margin-bottom: -5px; /* Margin bawah SVG */
            margin-right: 5px; /* Margin kanan SVG */
            width: 25px; /* Lebar SVG */
        }
        .further {
            background-color: rgba(247, 248, 249, 1); /* Background section further */
            border-bottom: 1px solid rgba(242, 242, 242, 1); /* Border bawah */
            border-top: 1px solid rgba(242, 242, 242, 1); /* Border atas */
        }
        .further h2:first-of-type {
            padding-top: 0; /* Hilangkan padding atas untuk H2 pertama */
        }
        .svg-stroke {
            fill: none; /* Hilangkan fill */
            stroke: #000; /* Warna garis */
            stroke-width: 32px; /* Ketebalan garis */
        }
        footer {
            background-color: rgba(221, 72, 20, .8); /* Background footer */
            text-align: center; /* Teks center */
        }
        footer .environment {
            color: rgba(255, 255, 255, 1); /* Warna teks */
            padding: 2rem 1.75rem; /* Padding */
        }
        footer .copyrights {
            background-color: rgba(62, 62, 62, 1); /* Background */
            color: rgba(200, 200, 200, 1); /* Warna teks */
            padding: .25rem 1.75rem; /* Padding */
        }
        @media (max-width: 629px) { /* Responsif mobile */
            header ul {
                padding: 0; /* Hilangkan padding */
            }
            header .menu-toggle {
                padding: 0 1rem; /* Padding horizontal */
            }
            header .menu-item {
                background-color: rgba(244, 245, 246, 1); /* Background menu item */
                border-top: 1px solid rgba(242, 242, 242, 1); /* Border top */
                margin: 0 15px; /* Margin horizontal */
                width: calc(100% - 30px); /* Lebar menu item */
            }
            header .menu-toggle {
                display: block; /* Tampilkan toggle */
            }
            header .hidden {
                display: none; /* Elemen hidden tetap tidak tampil */
            }
            header li.menu-item a {
                background-color: rgba(221, 72, 20, .1); /* Background link */
            }
            header li.menu-item a:hover,
            header li.menu-item a:focus {
                background-color: rgba(221, 72, 20, .7); /* Background hover/focus */
                color: rgba(255, 255, 255, .8); /* Warna teks hover/focus */
            }
        }
    </style>
</head>
<body> <!-- Mulai body halaman -->

<!-- HEADER: MENU + HERO SECTION -->
<header>
    <div class="menu">
        <ul>
            <!-- Logo CodeIgniter -->
            <li class="logo">
                <a href="https://codeigniter.com" target="_blank">
                    <!-- SVG Logo -->
                    <svg role="img" aria-label="Visit CodeIgniter.com official website!" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2100 500" height="44"><path fill="#dd4814" d="M148.2 411c..."/></svg>
                </a>
            </li>
            <!-- Tombol toggle menu mobile -->
            <li class="menu-toggle">
                <button id="menuToggle">&#9776;</button>
            </li>
            <!-- Menu items -->
            <li class="menu-item hidden"><a href="#">Home</a></li>
            <li class="menu-item hidden"><a href="https://codeigniter.com/user_guide/" target="_blank">Docs</a></li>
            <li class="menu-item hidden"><a href="https://forum.codeigniter.com/" target="_blank">Community</a></li>
            <li class="menu-item hidden"><a href="https://codeigniter.com/contribute" target="_blank">Contribute</a></li>
        </ul>
    </div>

    <!-- Hero section -->
    <div class="heroe">
        <h1>Welcome to CodeIgniter <?= CodeIgniter\CodeIgniter::CI_VERSION ?></h1> <!-- Menampilkan versi CI -->
        <h2>The small framework with powerful features</h2>
    </div>
</header>

<!-- CONTENT -->
<section>
    <h1>About this page</h1>
    <p>The page you are looking at is being generated dynamically by CodeIgniter.</p>
    <p>If you would like to edit this page you will find it located at:</p>
    <pre><code>app/Views/welcome_message.php</code></pre>
    <p>The corresponding controller for this page can be found at:</p>
    <pre><code>app/Controllers/Home.php</code></pre>
</section>

<!-- FURTHER SECTION -->
<div class="further">
    <section>
        <h1>Go further</h1>
        <!-- Learn -->
        <h2><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><rect x='32' y='96' width='64' height='368' rx='16' ry='16' class="svg-stroke" /><line x1='112' y1='224' x2='240' y2='224' class="svg-stroke" /><line x1='112' y1='400' x2='240' y2='400' class="svg-stroke" /><rect x='112' y='160' width='128' height='304' rx='16' ry='16' class="svg-stroke" /><rect x='256' y='48' width='96' height='416' rx='16' ry='16' class="svg-stroke" /><path d='M422.46,96.11l-40.4,4.25c-11.12,1.17-19.18,11.57-17.93,23.1l34.92,321.59c1.26,11.53,11.37,20,22.49,18.84l40.4-4.25c11.12-1.17,19.18-11.57,17.93-23.1L445,115C443.69,103.42,433.58,94.94,422.46,96.11Z' class="svg-stroke"/></svg> Learn</h2>
        <p>The User Guide contains an introduction, tutorial, a number of "how to" guides, and reference documentation. Check the <a href="https://codeigniter.com/user_guide/" target="_blank">User Guide</a>!</p>
        <!-- Discuss -->
        <h2><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><path d='M431,320.6c-1-3.6,1.2-8.6,3.3-12.2a33.68,33.68,0,0,1,2.1-3.1A162,162,0,0,0,464,215c.3-92.2-77.5-167-173.7-167C206.4,48,136.4,105.1,120,180.9a160.7,160.7,0,0,0-3.7,34.2c0,92.3,74.8,169.1,171,169.1,15.3,0,35.9-4.6,47.2-7.7s22.5-7.2,25.4-8.3a26.44,26.44,0,0,1,9.3-1.7,26,26,0,0,1,10.1,2L436,388.6a13.52,13.52,0,0,0,3.9,1,8,8,0,0,0,8-8,12.85,12.85,0,0,0-.5-2.7Z' class="svg-stroke" /><path d='M66.46,232a146.23,146.23,0,0,0,6.39,152.67c2.31,3.49,3.61,6.19,3.21,8s-11.93,61.87-11.93,61.87a8,8,0,0,0,2.71,7.68A8.17,8.17,0,0,0,72,464a7.26,7.26,0,0,0,2.91-.6l56.21-22a15.7,15.7,0,0,1,12,.2c18.94,7.38,39.88,12,60.83,12A159.21,159.21,0,0,0,284,432.11' class="svg-stroke" /></svg> Discuss</h2>
        <p>CodeIgniter is community-driven. View threads on <a href="https://forum.codeigniter.com/" target="_blank">forum</a> or <a href="https://join.slack.com/t/codeigniterchat/shared_invite/zt-rl30zw00-obL1Hr1q1ATvkzVkFp8S0Q" target="_blank">chat on Slack</a>!</p>
        <!-- Contribute -->
        <h2><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 512 512'><line x1='176' y1='48' x2='336' y2='48' class="svg-stroke" /><line x1='118' y1='304' x2='394' y2='304' class="svg-stroke" /><path d='M208,48v93.48a64.09,64.09,0,0,1-9.88,34.18L73.21,373.49C48.4,412.78,76.63,464,123.08,464H388.92c46.45,0,74.68-51.22,49.87-90.51L313.87,175.66A64.09,64.09,0,0,1,304,141.48V48' class="svg-stroke" /></svg> Contribute</h2>
        <p>CodeIgniter accepts contributions from the community. Why not <a href="https://codeigniter.com/contribute" target="_blank">join us</a>?</p>
    </section>
</div>

<!-- FOOTER: DEBUG INFO + COPYRIGHTS -->
<footer>
    <div class="environment">
        <p>Page rendered in {elapsed_time} seconds using {memory_usage} MB of memory.</p>
        <p>Environment: <?= ENVIRONMENT ?></p>
    </div>

    <div class="copyrights">
        <p>&copy; <?= date('Y') ?> CodeIgniter Foundation. CodeIgniter is open source project released under the MIT open source licence.</p>
    </div>
</footer>

<!-- SCRIPTS -->
<script {csp-script-nonce}>
    // Toggle menu mobile
    document.getElementById("menuToggle").addEventListener('click', toggleMenu);
    function toggleMenu() {
        var menuItems = document.getElementsByClassName('menu-item'); // Ambil semua menu-item
        for (var i = 0; i < menuItems.length; i++) {
            var menuItem = menuItems[i];
            menuItem.classList.toggle("hidden"); // Tambah/hapus class hidden untuk tampilkan menu
        }
    }
</script>

</body>
</html>
