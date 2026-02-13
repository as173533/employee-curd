<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;

#[Layout('components.layouts.app')]
class UserCrud extends Component
{
    public $name = '';
    public $email = '';
    public $userId = null;

    public $isEdit = false;
    public $showTrash = false;


    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
    ];

    // open add modal
    public function create()
    {
        $this->resetFields();
        $this->isEdit = false;

        $this->dispatch('open-modal');
    }

    // open edit modal
    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;

        $this->isEdit = true;

        $this->dispatch('open-modal');
    }

    public function save()
    {
        $this->validate();

        User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
            ]
        );

        $this->dispatch('close-modal');
        $this->dispatch('toast', message: $this->isEdit ? 'User Updated' : 'User Created');
        $this->resetFields();
    }

    public function delete($id)
    {
        User::find($id)?->delete();

        $this->dispatch('toast', message: 'User Deleted');
    }
    public function restore($id)
    {
        User::withTrashed()->find($id)?->restore();
        $this->dispatch('toast', message: 'User Restored');
    }

    public function forceDelete($id)
    {
        User::withTrashed()->find($id)?->forceDelete();
        $this->dispatch('toast', message: 'User Permanently Deleted');
    }

    private function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->userId = null;
    }

    public function render()
    {
        $users = $this->showTrash
            ? User::onlyTrashed()->latest()->get()
            : User::latest()->get();

        return view('livewire.user-crud', compact('users'));
    }
}
