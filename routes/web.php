<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Home
Route::get("/", "HomeController@getHome")->name("home");

// Auth endpoints, when user is logged in
Route::group(["middleware" => "auth"], function() {

    // Search
    Route::get("zoeken", "SearchController@getSearch")->name("search");
    Route::post("zoeken", "SearchController@postSearch")->name("search.post");
    
    // Jobs
    Route::group(["prefix" => "projecten"], function() {

        Route::get("/", "Projects\ProjectController@getOverview")->name("projects");

        // Create jobs
        Route::get("project-toevoegen", "Projects\ProjectController@getCreate")->name("projects.create");
        Route::post("project-toevoegen", "Projects\ProjectController@postCreate")->name("projects.create.post");
        
        // View job
        Route::get("{slug}", "Projects\ProjectController@getView")->name("projects.view");
        
        // Update job
        Route::get("{slug}/aanpassen", "Projects\ProjectController@getEdit")->name("projects.edit");
        Route::post("{slug}/aanpassen", "Projects\ProjectController@postEdit")->name("projects.edit.post");
        
        // Delete job
        Route::get("{slug}/verwijderen", "Projects\ProjectController@getDelete")->name("projects.delete");
        Route::post("{slug}/verwijderen", "Projects\ProjectController@postDelete")->name("projects.delete.post");
        
    });

});

// Api endpoints
// TODO: Move these to the api.php file and add proper token-based authentication instead of session hijacking like this
Route::group(["prefix" => "api"], function() {

    // Project resources
    Route::group(["prefix" => "project-resources"], function() {
        Route::post("create", "Api\ProjectResourceController@postCreateResource")->name("api.projects.resources.create.post");
        Route::post("update", "Api\ProjectResourceController@postUpdateResource")->name("api.projects.resources.update.post");
        Route::post("delete", "Api\ProjectResourceController@postDeleteResource")->name("api.projects.resources.delete.post");
    });

    // Comments
    Route::group(["prefix" => "comments"], function() {
        Route::post("create", "Api\CommentController@postCreateComment")->name("api.comments.create.post");
        Route::post("update", "Api\CommentController@postUpdateComment")->name("api.comments.update.post");
        Route::post("delete", "Api\CommentController@postDeleteComment")->name("api.comments.delete.post");
    });

    // Team member applications
    Route::group(["prefix" => "team-member-applications"], function() {
        Route::post("create", "Api\TeamMemberApplicationController@postCreateApplication")->name("api.team-member-applications.create.post");
        Route::post("update", "Api\TeamMemberApplicationController@postUpdateApplication")->name("api.team-member-applications.update.post");
        Route::post("delete", "Api\TeamMemberApplicationController@postDeleteApplication")->name("api.team-member-applications.delete.post");
        Route::post("accept", "Api\TeamMemberApplicationController@postAcceptApplication")->name("api.team-member-applications.accept.post");
        Route::post("deny", "Api\TeamMemberApplicationController@postDenyApplication")->name("api.team-member-applications.deny.post");
    });

    // Team roles
    Route::group(["prefix" => "team-roles"], function() {
        Route::post("unassign", "Api\TeamRoleController@postUnassign")->name("api.team-roles.unassign");
    });
    
});

// Admin panel
Route::group(["prefix" => "admin", "middleware" => ["can:access-admin-panel"]], function() {

    Route::get("/", "Admin\DashboardController@getDashboard")->name("admin.dashboard");

});