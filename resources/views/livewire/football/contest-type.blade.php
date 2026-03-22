  <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">All Contest types</h5>
          <a href="{{ route('admin.football.contest.type.add') }}" class="btn btn-primary">Add New</a>
      </div>
      <div class="card-body">
          <div class="table-responsive">
              <table class="table table-striped">
                  <thead>
                      <tr>
                          <th>S.NO.</th>
                          <th>Contest type</th>
                          <th>Code</th>
                          <th>Status</th>
                          <th>Actions</th>
                      </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                      @if (count($contestTypes) < 1)
                          <tr>
                              <td colspan="10">
                                  <div class="d-flex justify-content-center mt-3">
                                      No Contest type record found.
                                  </div>
                              </td>
                          </tr>
                      @endif
                      @foreach ($contestTypes as $key => $data)
                          <tr>
                              <td>{{ $contestTypes->firstItem() + $key }}</td>
                              <td>{{ $data->name }}</td>
                              <td>{{ $data->code }}</td>
                              <td>
                                  @if (!$data->status)
                                      <span class="badge bg-label-danger">Deleted</span>
                                  @else
                                      <span class="badge bg-label-success">Active</span>
                                  @endif
                              </td>
                              <td>
                                  <button type="button" class="btn btn-outline-danger"
                                      wire:click="confirmDelete({{ $data->id }})">
                                      <i class="tf-icons bx bx-trash"></i>
                                  </button>
                              </td>
                          </tr>
                      @endforeach
                      @if ($contestTypes->hasPages())
                          <tr>
                              <td colspan="10">
                                  <div class="d-flex justify-content-center mt-3">
                                      {{ $contestTypes->appends(request()->query())->links('pagination::bootstrap-4') }}
                                  </div>
                              </td>
                          </tr>
                      @endif

                  </tbody>

              </table>
          </div>

      </div>
  </div>
