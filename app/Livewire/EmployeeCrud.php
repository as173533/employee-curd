<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Employee;

class EmployeeCrud extends Component
{
    use WithPagination;

    public $name, $email, $designation, $salary;
    public $employeeId;
    public $search = '';
    public $isEdit = false;

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'designation' => 'required',
        'salary' => 'required|numeric',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        Employee::updateOrCreate(
            ['id' => $this->employeeId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'designation' => $this->designation,
                'salary' => $this->salary,
            ]
        );

        session()->flash('success', $this->employeeId ? 'Employee updated' : 'Employee added');

        $this->reset();
    }

    public function edit($id)
    {
        $emp = Employee::findOrFail($id);

        $this->employeeId = $emp->id;
        $this->name = $emp->name;
        $this->email = $emp->email;
        $this->designation = $emp->designation;
        $this->salary = $emp->salary;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        Employee::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.employee-crud', [
            'employees' => Employee::where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->latest()
                ->paginate(5),
        ]);
    }
}
