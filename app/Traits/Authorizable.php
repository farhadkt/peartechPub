<?php
namespace App\Traits;

use App\Permission;
use App\Role;

trait Authorizable {

    public function roles() {
        return $this->belongsToMany(Role::class,'users_roles');
    }

    public function permissions() {
        return $this->belongsToMany(Permission::class,'users_permissions');
    }

    /**
     * Check whether user has specific role or not
     * @param mixed ...$roles
     * @return bool
     */
    public function hasRole(... $roles ) {
        foreach ($roles as $role) {
            if ($this->roles->contains('slug', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $role
     * Assign new role to user
     */
    public function assignRole($role) {
        $role = Role::where('slug', $role)->first();

        if ($role) $this->roles()->attach($role);
    }


    /**
     * Check if user has a role in both ways :
     * 1.Through permission's role
     * 2.Regardless of the role
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission) {
        return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
    }

    /**
     * Check whether user has a permission through permission role's
     * First get requested permission roles
     * then check user has that role
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission) {
        foreach ($permission->roles as $role){
            if($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Regardless of the role, check whether user has a permission or not.
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission) {
        return (bool) $this->permissions->where('slug', $permission->slug)->count();
    }

    /**
     * A wrapper for 2 methods:
     * 1.Assign role to user
     * 1.Assign related role permissions to user
     * @param $role
     */
    public function assignRoleAndPermissions($role)
    {
        $this->assignRole($role);
        $this->assignAllPermissionsFromRole($role);
    }

    /**
     * Attach requested role's permission to user permissions
     * @param $role
     */
    public function assignAllPermissionsFromRole($role)
    {
        $role = Role::with('permissions')
            ->where('slug', $role)
            ->first();

        $permissionIds = $role->permissions->pluck('id')->toArray();

        $this->permissions()->attach($permissionIds);
    }


    /**
     * A wrapper
     * Sync user permissions and roles
     *
     * @param mixed ...$roles
     */
    public function syncAllRolesAndPermission(...$roles)
    {
        $this->syncRoles($roles);
        $this->syncPermissionsFromRoles($roles);
    }

    /**
     * 1. Get all roles permission
     * 2. Sync user permissions
     *
     * @param mixed ...$roles
     */
    public function syncPermissionsFromRoles(...$roles)
    {
        $rolesObject = Role::with('permissions')->whereIn('slug', $roles);
        $permissions = [];
        foreach ($rolesObject->get() as $role) {
            $permissions = array_merge($permissions, $role->permissions()->pluck('id')->toArray());
        }
        $this->permissions()->sync($permissions);
    }

    /**
     * 1. Get all roles from database
     * 2. Sync user roles
     *
     * @param mixed ...$roles
     */
    public function syncRoles(...$roles)
    {
        $rolesObject = Role::whereIn('slug', $roles);
        $this->roles()->sync($rolesObject->pluck('id')->toArray());
    }

    public function deletePermissions( ... $permissions ) {
        $permissions = $this->getAllPermissions($permissions);
        $this->permissions()->detach($permissions);
        return $this;
    }
}
