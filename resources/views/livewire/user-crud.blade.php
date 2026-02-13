<div
    x-data="{
        open: false,
        confirmId: null,
        toast: false,
        message: ''
    }"

    {{-- modal open/close --}}
    x-on:open-modal.window="open = true"
    x-on:close-modal.window="open = false"

    {{-- toast --}}
    x-on:toast.window="
        message = $event.detail.message;
        toast = true;
        setTimeout(() => toast = false, 2000);
    "
>

    {{-- HEADER --}}
    <div style="display:flex; justify-content:space-between;">
        <h2>User Management</h2>
        <button wire:click="create">+ Add User</button>
    </div>

    <div style="margin-bottom:15px;">
        <button
            wire:click="$set('showTrash', false)"
            style="{{ !$showTrash ? 'background:black;color:white;' : '' }}"
        >
            Active Users
        </button>

        <button
            wire:click="$set('showTrash', true)"
            style="margin-left:5px; {{ $showTrash ? 'background:black;color:white;' : '' }}"
        >
            Trash
        </button>
    </div>
    {{-- TABLE --}}
    <table border="1" width="100%" cellpadding="8" style="margin-top:15px;">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th width="200">Action</th>
        </tr>

        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>

                    @if(!$showTrash)
                    <button wire:click="edit({{ $user->id }})">Edit</button>

                    <button
                        @click="confirmId = {{ $user->id }}"
                        style="color:red; margin-left:5px;"
                    >
                        Delete
                    </button>
                    
                @else
                    <button wire:click="restore({{ $user->id }})">
                        Restore
                    </button>

                    <button
                        wire:click="forceDelete({{ $user->id }})"
                        style="color:red; margin-left:5px;"
                    >
                        Delete Permanently
                    </button>
                @endif

                </td>
            </tr>
        @endforeach
    </table>



    {{-- ================= MODAL ================= --}}
    <div
        x-show="open"
        x-transition
        @keydown.escape.window="open = false"
        @click.self="open = false"
        style="position:fixed; inset:0; background:rgba(0,0,0,.5);"
    >
        <div style="background:white; width:400px; margin:100px auto; padding:20px; border-radius:6px;">

            <div style="display:flex; justify-content:space-between;">
                <h3>{{ $isEdit ? 'Edit User' : 'Add User' }}</h3>
                <button @click="open = false">✖</button>
            </div>

            <hr>

            <form wire:submit.prevent="save">

                <div>
                    <label>Name</label>
                    <input wire:model="name" style="width:100%;">
                    @error('name') <div style="color:red">{{ $message }}</div> @enderror
                </div>

                <div style="margin-top:10px;">
                    <label>Email</label>
                    <input wire:model="email" style="width:100%;">
                    @error('email') <div style="color:red">{{ $message }}</div> @enderror
                </div>

                <div style="margin-top:15px; text-align:right;">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove>
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </span>

                        <span wire:loading>
                            Saving...
                        </span>
                    </button>

                    <button type="button" @click="open = false">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>



    {{-- ================= DELETE CONFIRM ================= --}}
    <div
        x-show="confirmId"
        x-transition
        style="position:fixed; inset:0; background:rgba(0,0,0,.5);"
    >
        <div style="background:white; width:300px; margin:150px auto; padding:20px; border-radius:6px;">
            <h4>Are you sure?</h4>

            <div style="margin-top:15px; text-align:right;">
                <button
                    @click="$wire.delete(confirmId); confirmId = null"
                    style="color:red;"
                >
                    Yes, Delete
                </button>

                <button @click="confirmId = null">
                    Cancel
                </button>
            </div>
        </div>
    </div>



    {{-- ================= TOAST ================= --}}
    <div
        x-show="toast"
        x-transition
        style="position:fixed; top:20px; right:20px; background:black; color:white; padding:10px 15px; border-radius:5px;"
    >
        <span x-text="message"></span>
    </div>

</div>
