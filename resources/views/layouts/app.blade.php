@include('components.head')
@include('components.nav')
<!-- Page Content -->
<main>
    {{ $slot }}
</main>
@include('components.footer')
</body>

</html>
