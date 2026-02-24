# Admin Panel

## Setup

The application includes a comprehensive admin panel for managing users, posts, categories, and system settings.
 Accessing the Admin Panel
1. **Setup the database**
      php artisan migrate:fresh --seed
2. Navigate to admin panel
   
   Visit /admin in your browser after logging in with an admin account.
3. Create your first admin user
   
   If you don't have an admin account, create one via Tinker:
      php artisan tinker
      
      $user = App\Models\User::create([
       'name' => 'Your Name',
       'email' => 'your-email@example.com',
       'password' => Hash::make('your-secure-password')
   ]);
   
   $role = App\Models\Role::where('name', 'super_admin')->first();
   $user->roles()->attach($role->id);
   
> ⚠️ Security Warning: Always use strong, unique passwords for admin accounts. Never use default or weak passwords in production.
---
Admin Features
📊 Dashboard
- Overview statistics (posts, users, comments, likes)
- Recent posts and comments
- Recent activity log
- Posts status breakdown (published, pending, draft, archived)
👥 User Management
- View all users with search and filter capabilities
- View user profiles and activity history
- Edit user information and roles
- Ban/unban users with reason tracking
- Bulk actions (ban, unban, delete)
- Role-based editing restrictions
📝 Post Management
- View all posts with status filtering
- Approve pending posts
- Archive published posts
- Edit any post content
- Delete posts
- Bulk actions (approve, archive, delete)
📁 Category Management
- Create new categories
- Edit existing categories
- Delete categories (with post association check)
- View post count per category
⚙️ Settings
- System configuration (Super Admin only)
📋 Audit Logs
- All admin actions are automatically logged
- Tracks user, action, resource, timestamps, and IP address
- Viewable by Super Admin only
---
Role-Based Access Control
The application uses a role-permission system for fine-grained access control.
Available Roles
| Role | Description |
|------|-------------|
| Super Admin | Full access to all features including settings and audit logs |
| Admin | Administrative access to most features |
| Moderator | Content moderation and user management |
| Editor | Content editing capabilities |
Permissions Matrix
| Feature | Super Admin | Admin | Moderator | Editor |
|---------|:-----------:|:-----:|:---------:|:------:|
| Access Admin Panel | ✅ | ✅ | ✅ | ✅ |
| Manage Users | ✅ | ✅ | ✅ | ✅ |
| Ban Users | ✅ | ✅ | ✅ | ❌ |
| Manage Posts | ✅ | ✅ | ✅ | ✅ |
| Approve Posts | ✅ | ✅ | ✅ | ❌ |
| Delete Posts | ✅ | ✅ | ❌ | ❌ |
| Manage Categories | ✅ | ✅ | ❌ | ❌ |
| View Audit Logs | ✅ | ❌ | ❌ | ❌ |
| Access Settings | ✅ | ❌ | ❌ | ❌ |
---
Post Approval Workflow
User-submitted posts follow an approval workflow:
User creates post → Pending → Admin/Moderator approves → Published
                                                    ↓
                                              Admin archives → Archived
- Pending: New posts await approval (not visible to public)
- Published: Approved posts visible to everyone
- Archived: Hidden from public view
- Draft: Work-in-progress (future feature)
---
Managing Roles & Permissions
Assign Role to User
php artisan tinker
$user = App\Models\User::find(1);
$role = App\Models\Role::where('name', 'moderator')->first();
$user->roles()->attach($role->id);
Remove Role from User
$user = App\Models\User::find(1);
$role = App\Models\Role::where('name', 'moderator')->first();
$user->roles()->detach($role->id);
Check User Permissions
$user = App\Models\User::find(1);
$user->hasRole('admin');           // Check role
$user->hasPermission('manage_posts'); // Check permission
---
Admin Routes
| Route | Method | Description |
|-------|--------|-------------|
| /admin | GET | Admin dashboard |
| /admin/settings | GET | System settings (Super Admin only) |
| /admin/users | GET | List all users |
| /admin/users/{user} | GET | View user details |
| /admin/users/{user}/edit | GET | Edit user form |
| /admin/users/{user} | PUT | Update user |
| /admin/users/{user}/ban | POST | Ban user |
| /admin/users/{user}/unban | POST | Unban user |
| /admin/users/{user} | DELETE | Delete user |
| /admin/posts | GET | List all posts |
| /admin/posts/{post}/approve | POST | Approve post |
| /admin/posts/{post}/archive | POST | Archive post |
| /admin/posts/{post} | DELETE | Delete post |
| /admin/categories | GET | List categories |
| /admin/categories/create | GET | Create category form |
| /admin/categories | POST | Store category |
| /admin/categories/{category}/edit | GET | Edit category form |
| /admin/categories/{category} | PUT | Update category |
| /admin/categories/{category} | DELETE | Delete category |