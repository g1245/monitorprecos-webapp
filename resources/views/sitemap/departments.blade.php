<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($departments as $department)
    <url>
        <loc>{{ route('department.index', ['alias' => \Illuminate\Support\Str::of($department->name)->slug(), 'departmentId' => $department->id]) }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
@endforeach
</urlset>
