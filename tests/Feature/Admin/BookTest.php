<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BookTest extends TestCase
{
    use RefreshDatabase;

    protected $superAdmin;
    protected $adminWithManage;
    protected $adminWithViewOnly;
    protected $student;
    protected $teacher;
    protected $parent;
    protected $staffWithoutPermission;
    protected $book;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);
        $parentRole = Role::firstOrCreate(['name' => 'Parent']);

        // Create permissions
        $manageBooks = Permission::firstOrCreate(['name' => 'manage books']);
        $viewBooks = Permission::firstOrCreate(['name' => 'view books']);
        $viewDashboard = Permission::firstOrCreate(['name' => 'view dashboard']);

        $adminRole->givePermissionTo([$viewDashboard]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create SuperAdmin
        $this->superAdmin = User::factory()->create(['role' => 'SuperAdmin']);
        $this->superAdmin->assignRole('SuperAdmin');

        // Create Admin with manage books
        $this->adminWithManage = User::factory()->create(['role' => 'Admin']);
        $this->adminWithManage->assignRole('Admin');
        $this->adminWithManage->givePermissionTo($manageBooks);

        // Create Admin with view books only
        $this->adminWithViewOnly = User::factory()->create(['role' => 'Admin']);
        $this->adminWithViewOnly->assignRole('Admin');
        $this->adminWithViewOnly->givePermissionTo($viewBooks);

        // Create other roles
        $this->student = User::factory()->create(['role' => 'Student']);
        $this->student->assignRole('Student');

        $this->teacher = User::factory()->create(['role' => 'Teacher']);
        $this->teacher->assignRole('Teacher');

        $this->parent = User::factory()->create(['role' => 'Parent']);
        $this->parent->assignRole('Parent');

        // Create staff without permission
        $this->staffWithoutPermission = User::factory()->create(['role' => 'Admin']);
        $this->staffWithoutPermission->assignRole('Admin');

        // Create test book
        $this->book = Book::create([
            'title' => 'Test Book',
            'description' => 'Test Description',
            'file_path' => 'books/test-book.pdf',
            'is_active' => true,
        ]);
        
        Storage::disk('local')->put('books/test-book.pdf', 'fake pdf content');
    }

    public function test_superadmin_can_access_books_index()
    {
        $this->actingAs($this->superAdmin);
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);
    }

    public function test_admin_with_manage_permission_can_access_books_index()
    {
        $this->actingAs($this->adminWithManage);
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);
    }

    public function test_admin_with_view_permission_can_access_books_index()
    {
        $this->actingAs($this->adminWithViewOnly);
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);
    }

    public function test_staff_without_permission_cannot_access_books_index()
    {
        $this->actingAs($this->staffWithoutPermission);
        $response = $this->get(route('books.index'));
        $response->assertStatus(403);
    }

    public function test_students_teachers_and_parents_can_access_books_index_without_explicit_permissions()
    {
        // Student
        $this->actingAs($this->student);
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);

        // Teacher
        $this->actingAs($this->teacher);
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);

        // Parent
        $this->actingAs($this->parent);
        $response = $this->get(route('books.index'));
        $response->assertStatus(200);
    }

    public function test_admin_with_manage_permission_can_store_book()
    {
        $this->actingAs($this->adminWithManage);

        $pdf = UploadedFile::fake()->create('new-book.pdf', 100, 'application/pdf');
        $cover = UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg');

        $response = $this->post(route('books.store'), [
            'title' => 'New Seeding Book',
            'description' => 'New Description',
            'pdf_file' => $pdf,
            'cover_image' => $cover,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('books.index'));
        
        $this->assertDatabaseHas('books', [
            'title' => 'New Seeding Book',
        ]);

        $book = Book::where('title', 'New Seeding Book')->first();
        Storage::disk('local')->assertExists($book->file_path);
        Storage::disk('public')->assertExists($book->cover_image);
    }

    public function test_admin_with_view_only_permission_cannot_store_book()
    {
        $this->actingAs($this->adminWithViewOnly);

        $pdf = UploadedFile::fake()->create('new-book.pdf', 100, 'application/pdf');

        $response = $this->post(route('books.store'), [
            'title' => 'Should Fail Book',
            'pdf_file' => $pdf,
        ]);

        $response->assertStatus(403);
    }

    public function test_authorized_users_can_stream_pdf()
    {
        $this->actingAs($this->student);

        $response = $this->get(route('books.stream', $this->book));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_admin_can_store_book_via_ajax()
    {
        $this->actingAs($this->adminWithManage);

        $pdf = UploadedFile::fake()->create('ajax-book.pdf', 100, 'application/pdf');

        $response = $this->post(route('books.store'), [
            'title' => 'Ajax Book Title',
            'description' => 'Ajax Book Description',
            'pdf_file' => $pdf,
            'is_active' => '1',
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect' => route('books.index')
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Ajax Book Title',
        ]);
    }

    public function test_ajax_store_fails_with_validation_errors()
    {
        $this->actingAs($this->adminWithManage);

        $response = $this->post(route('books.store'), [
            'title' => '', // missing title
        ], [
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'success',
            'errors' => [
                'title',
                'pdf_file'
            ]
        ]);
    }
}
