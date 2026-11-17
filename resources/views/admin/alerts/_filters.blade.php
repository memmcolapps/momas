@php($action = $action ?? url('admin/alerts'))

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ $action }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fs-13">Alert Title</label>
                <input type="text" name="title" value="{{ request('title') }}" class="form-control" placeholder="Search alert title...">
            </div>

            <div class="col-md-8">
                <label class="form-label fs-13">Created At</label>
                <div class="input-group">
                    <input type="date" name="created_from" value="{{ request('created_from') }}" class="form-control">
                    <span class="input-group-text">to</span>
                    <input type="date" name="created_to" value="{{ request('created_to') }}" class="form-control">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fs-13">Displays At</label>
                <div class="input-group">
                    <input type="date" name="displays_from" value="{{ request('displays_from') }}" class="form-control">
                    <span class="input-group-text">to</span>
                    <input type="date" name="displays_to" value="{{ request('displays_to') }}" class="form-control">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fs-13">Expires At</label>
                <div class="input-group">
                    <input type="date" name="expires_from" value="{{ request('expires_from') }}" class="form-control">
                    <span class="input-group-text">to</span>
                    <input type="date" name="expires_to" value="{{ request('expires_to') }}" class="form-control">
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <a href="{{ $action }}" class="btn btn-outline-secondary">
                    <i data-feather="rotate-ccw" class="me-1"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-feather="filter" class="me-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>
