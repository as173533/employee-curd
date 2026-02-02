<div class="container mt-5">

    <h3>Employee CRUD</h3>

    @if(session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <input class="form-control mb-3" placeholder="Search employee..."
           wire:model.live="search">

    <form wire:submit.prevent="save" class="row g-2">
        <div class="col-md-3">
            <input wire:model="name" class="form-control" placeholder="Name">
        </div>
        <div class="col-md-3">
            <input wire:model="email" class="form-control" placeholder="Email">
        </div>
        <div class="col-md-3">
            <input wire:model="designation" class="form-control" placeholder="Designation">
        </div>
        <div class="col-md-2">
            <input wire:model="salary" type="number" class="form-control" placeholder="Salary">
        </div>
        <div class="col-md-1">
            <button class="btn btn-primary w-100">
                {{ $isEdit ? 'Update' : 'Add' }}
            </button>
        </div>
    </form>

    <table class="table table-bordered mt-4">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Designation</th>
            <th>Salary</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($employees as $emp)
            <tr>
                <td>{{ $emp->id }}</td>
                <td>{{ $emp->name }}</td>
                <td>{{ $emp->email }}</td>
                <td>{{ $emp->designation }}</td>
                <td>{{ $emp->salary }}</td>
                <td>
                    <button wire:click="edit({{ $emp->id }})" class="btn btn-sm btn-warning">Edit</button>
                    <button wire:click="delete({{ $emp->id }})"
                        onclick="confirm('Delete employee?') || event.stopImmediatePropagation()"
                        class="btn btn-sm btn-danger">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $employees->links() }}

</div>
