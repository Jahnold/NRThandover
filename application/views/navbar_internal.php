<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Matthew
 * Date: 25/01/14
 * Time: 16:25
 * To change this template use File | Settings | File Templates.
 */?>
<nav class="navbar navbar-default navbar-fixed-top navbar-inverse" role="navigation"
     xmlns="http://www.w3.org/1999/html">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="container">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo site_url()?>">NRT Handover</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
            <li><a href="<?php echo site_url()?>/dashboard">Home</a></li>
            <li><a href="<?php echo site_url()?>/reports">Reports</a></li>
            <li><a href="<?php echo site_url()?>/admin">Admin</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="<?php echo site_url('auth/logout') ?>">Log Out</a>
        </ul>
    </div><!-- /.navbar-collapse -->
    </div>
</nav>