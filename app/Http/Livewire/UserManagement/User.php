<?php

namespace App\Http\Livewire\UserManagement;

use App\Models\Role;
use App\Models\Team;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class User extends Component
{
    public $users_id;
    public $role_id;
    public $team_id = 1;
    public $name;
    public $email;
    public $nidn;
    public $password;


    public $form_active = false;
    public $form = false;
    public $update_mode = false;
    public $modal = true;

    public function render()
    {
        return view('livewire.usermanagement.users', [
            'items' => ModelsUser::whereHas('roles', function ($query) {
                return $query->whereNotIn('role_type', ['superadmin', 'admin']);
            })->get(),
            'roles' => Role::whereNotIn('role_type', ['superadmin', 'admin'])->get()
        ]);
    }

    public function store()
    {
        $this->_validate();
        $role = Role::find($this->role_id);
        $user = ModelsUser::create([
            'name'  => $this->name,
            'email'  => $this->email,
            'username'  => $this->nidn,
            'password'  => Hash::make($role->role_type . '123')
        ]);

        $team = Team::find($this->team_id);
        $team->users()->attach($user, ['role' => $role->role_type]);
        $role->users()->attach($user);
        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Disimpan']);
    }

    public function update()
    {
        $this->_validate();
        $user = ModelsUser::find($this->users_id);
        $role = Role::find($this->role_id);
        $user->update([
            'name'  => $this->name,
            'email'  => $this->email,
            'username'  => $this->nidn,
        ]);

        $team = Team::find($this->team_id);
        $team->users()->sync($user, ['role' => $role->role_type]);
        $role->users()->sync($user);

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Diupdate']);
    }

    public function delete()
    {
        $user = ModelsUser::find($this->users_id);
        $user->delete();

        $this->_reset();
        return $this->emit('showAlert', ['msg' => 'Data Berhasil Dihapus']);
    }

    public function _validate()
    {
        $rule = [
            'name'  => 'required',
            'email'  => 'required',
            'nidn'  => 'required|numeric',
            'role_id'  => 'required'
        ];

        return $this->validate($rule);
    }

    public function getDataById($users_id)
    {
        $users = ModelsUser::find($users_id);
        $this->users_id = $users->id;
        $this->name = $users->name;
        $this->email = $users->email;
        $this->nidn = $users->username;
        $this->password = $users->password;
        $this->role_id = $users->role->id;
        if ($this->form) {
            $this->form_active = true;
            $this->emit('loadForm');
        }
        if ($this->modal) {
            $this->emit('showModal');
        }
        $this->update_mode = true;
    }

    public function getId($users_id)
    {
        $users = ModelsUser::find($users_id);
        $this->users_id = $users->id;
    }

    public function toggleForm($form)
    {
        $this->form_active = $form;
        $this->emit('loadForm');
    }

    public function showModal()
    {
        $this->emit('showModal');
    }

    public function _reset()
    {
        $this->emit('closeModal');
        $this->users_id = null;
        $this->role_id = null;
        $this->team_id = 1;
        $this->name = null;
        $this->email = null;
        $this->password = null;
        $this->form = false;
        $this->form_active = false;
        $this->update_mode = false;
        $this->modal = true;
    }
}
