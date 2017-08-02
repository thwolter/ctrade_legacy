<div class="tab-pane fade in active" id="profile-tab">

    <div class="heading-block">
        <h3>
            Mein Profil
        </h3>
    </div> <!-- /.heading-block -->

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula
        eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient
        montes. Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>

    <br><br>

    {!! Form::open(['route' => ['users.update', $user->id], 'method' => 'PUT',
        'class' => 'form-horizontal']) !!}

        <div class="form-group">

            <label class="col-md-3 control-label">Name</label>

            <div class="col-md-7">
                <input type="text" name="name" value="{{ $user->name }}"
                       class="form-control"/>
            </div> <!-- /.col -->

        </div> <!-- /.form-group -->



        <div class="form-group">

            <label class="col-md-3 control-label">Email Addresse</label>

            <div class="col-md-7">
                <input type="text" name="email-address" value="{{ $user->email }}"
                       class="form-control"/>
            </div> <!-- /.col -->

        </div> <!-- /.form-group -->



        <div class="form-group">
            <div class="col-md-7 col-md-push-3">
                <button type="submit" class="btn btn-primary">Speichern</button>
                &nbsp;
                <button type="reset" class="btn btn-default">Abbrechen</button>
            </div> <!-- /.col -->
        </div> <!-- /.form-group -->

    {!! Form::close() !!}


</div> <!-- /.tab-pane -->