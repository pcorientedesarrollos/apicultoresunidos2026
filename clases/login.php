<div class="col-lg-6 col-lg-offset-3">
    <div class="login-box" >
        <br>
        <div class="login-logo">
            <a href=""><b>Portal Administrativo</b></a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <div class="form-group has-feedback">
                <input type="text"
                        id="user"
                        name="user"
                       ng-model="usuario"
                       class="form-control"
                       placeholder="Usuario">
                <span class="form-control-feedback"><i class="zmdi zmdi-account"></i></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" 
                        id="password"
                        name="password"
                       ng-model="password"
                       class="form-control" 
                       placeholder="Contraseña">
                <span class="form-control-feedback">
                    <i class="zmdi zmdi-key"></i>
                </span>
            </div>
            <div class="form-group has-feedback">
                <select name="selectYear" id="selectYear" ng-model="selectYear">
                    <option value="2019">2019</option>
                </select>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button
                        type="submit" 
                        class="btn btn-facebook btn-block btn-flat"
                        ng-click="acceder()">Ingresar</button>
                </div><!-- /.col -->
            </div>
            <br>
            <div class="row">
                <div class="col-xs-6">
                    <a href="../"> <i class="fa fa-sign-in"></i>  Versión actual</a>
                </div>
            </div>
        </div> 
    </div>
</div>