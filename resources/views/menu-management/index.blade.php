<x-app-layout>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">Manajemen Akses Menu</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('menu-management.store') }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%">Menu</th>
                                @foreach($roles as $role)
                                    <th class="text-center">
                                        {{ ucfirst(strtolower($role->name)) }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menus as $menu)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $menu->name }}
                                    </td>
                                    @foreach($roles as $role)
                                        <td class="text-center">
                                            <div class="form-check d-flex justify-content-center">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="access[{{ $menu->id }}][{{ $role->id }}]"
                                                    id="menu_{{ $menu->id }}_role_{{ $role->id }}"
                                                    {{ isset($roleMenuAccess[$menu->id][$role->id]) ? 'checked' : '' }}
                                                >
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i> Simpan Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
