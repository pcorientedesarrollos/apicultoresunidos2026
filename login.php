<div class="col-lg-6 col-lg-offset-3">
    <div class="login-box">
        <br>
        <div class="login-logo">
            <a href=""><b>Portal Administrativo</b></a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <div class="form-group has-feedback">
                <input type="text" id="user" name="user" ng-model="usuario" class="form-control" placeholder="Usuario">
                <span class="form-control-feedback"><i class="zmdi zmdi-account"></i></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" id="password" name="password" ng-model="password" class="form-control" placeholder="Contraseña">
                <span class="form-control-feedback">
                    <i class="zmdi zmdi-key"></i>
                </span>
            </div>
            <div class="form-group has-feedback">
                <select name="selectYear" id="selectYear" ng-model="selectYear">
                    <option value="2026">2026</option>
                </select>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-facebook btn-block btn-flat" ng-click="acceder()">Ingresar</button>
                </div><!-- /.col -->
            </div>
            <br>
            <div class="row">
                <div class="col-xs-12">
                    <a href="./v8/"> <i class="fa fa-sign-in"></i> Ejercicio 2025</a>
                    <br>
                    <a href="./v7/"> <i class="fa fa-sign-in"></i> Ejercicio 2024</a>
                    <br>
                    <a href="./v6/"> <i class="fa fa-sign-in"></i> Ejercicio 2023</a>
                    <br>
                    <a href="./v5/"> <i class="fa fa-sign-in"></i> Ejercicio 2022</a>
                    <br>
                    <a href="./v4/"> <i class="fa fa-sign-in"></i> Ejercicio 2021</a>
                    <br>
                    <a href="./v4/v3"> <i class="fa fa-sign-in"></i> Ejercicio 2020</a>
                    <br>
                    <a href="./v4/v2/"> <i class="fa fa-sign-in"></i> Ejercicio 2019</a>
                    <!-- <a href="./v1/"> <i class="fa fa-sign-in"></i>  Versión anterior</a> -->
                </div>
            </div>
        </div>
    </div>
</div>