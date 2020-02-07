<?php

namespace App\Services\ModelServices;

use Auth;
use Dates;
use Users;
use Skills;
use Uploader;
use TeamRoles;
use WorkMethods;
use JobStatuses;
use JobResources;
use JobCategories;
use TeamMemberApplications;
use App\Models\Job;
use App\Models\TeamRole;
use App\Http\Requests\Jobs\CreateJobRequest;
use App\Http\Requests\Jobs\UpdateJobRequest;

class JobService
{
    private $jobs;
    private $preloadedJobs;

    public function getAll()
    {
        if (is_null($this->jobs))
        {
            $this->jobs = Job::all();
        }

        return $this->jobs;
    }

    public function getAllPreloaded()
    {
        if (is_null($this->preloadedJobs))
        {
            $out = [];

            foreach ($this->getAll() as $job)
            {
                $out[] = $this->preload($job);
            }

            return collect($out);
        }

        return $this->preloadedJobs;
    }

    public function find($id)
    {
        foreach ($this->getAll() as $job)
        {
            if ($job->id == $id)
            {
                return $job;
            }
        }

        return false;
    }

    public function findBySlug($slug)
    {
        foreach ($this->getAll() as $job)
        {
            if ($job->slug == $slug)
            {
                return $job;
            }
        }

        return false;
    }
    
    public function findPreloaded($id)
    {
        foreach ($this->getAllPreloaded() as $job)
        {
            if ($job->id == $id)
            {
                return $job;
            }
        }

        return false;
    }

    public function findPreloadedBySlug($slug)
    {
        foreach ($this->getAllPreloaded() as $job)
        {
            if ($job->slug == $slug)
            {
                return $job;
            }
        }

        return false;
    }

    private function preload(Job $job)
    {
        // Convert header image url from relative to absolute (so it can be used in vue components)
        $job->header_image_url = asset($job->header_image_url);

        // Load the job's resources
        $job->resources = JobResources::getAllPreloadedForJob($job);

        // Load the job's team roles
        $job->team_roles = TeamRoles::getAllPreloadedForJob($job);

        // Load the job's status
        $job->status = JobStatuses::findForJob($job);

        // Load the job's author
        $job->author = Users::findAuthorForJob($job);

        // Load the job's category
        $job->category = JobCategories::findForJob($job);

        // Load the job's work method
        $job->work_method = WorkMethods::findForJob($job);

        // Load the job's team member applications
        $job->team_member_applications = TeamMemberApplications::getAllForJob($job);

        // Format the dates
        $job->formatted_starts_at = is_null($job->starts_at) ? null : $job->starts_at->format("d-m-Y");
        $job->formatted_ends_at = is_null($job->ends_at) ? null : $job->ends_at->format("d-m-Y");
        $job->formatted_created_at = $job->created_at->format("d-m-Y H:m:s");
        $job->formatted_updated_at = $job->updated_at->format("d-m-Y H:m:s");

        // Return the upgraded job
        return $job;
    }

    public function createFromRequest(CreateJobRequest $request)
    {
        $starts_at = Dates::parse($request->starts_at, "/");
        $ends_at = Dates::parse($request->ends_at, "/");

        $data = [
            "author_id" => Auth::user()->id,
            "job_status_id" => $request->job_status_id,
            "job_category_id" => $request->job_category_id,
            "work_method_id" => $request->work_method_id,
            "title" => $request->title,
            "slogan" => $request->slogan,
            "problem" => $request->problem,
            "description" => $request->description,
            "starts_at" => $starts_at->format("Y-m-d"),
            "ends_at" => $ends_at->format("Y-m-d"),
        ];

        if ($request->hasFile("header_image"))
        {
            $data["header_image_url"] = Uploader::upload($request->file("header_image"), "images/jobs/header");
        }

        $job = Job::create($data);

        $this->processJobResources($job, $request->resources);
        $this->processTeamRoles($job, $request->team_roles);

        return $job;
    }
    
    public function updateFromRequest(Job $job, UpdateJobRequest $request)
    {
        $starts_at = Dates::parse($request->starts_at, "/");
        $ends_at = Dates::parse($request->ends_at, "/");

        $job->job_status_id = $request->job_status_id;
        $job->job_category_id = $request->job_category_id;
        $job->work_method_id = $request->work_method_id;
        $job->title = $request->title;
        $job->slogan = $request->slogan;
        $job->problem = $request->problem;
        $job->description = $request->description;
        $job->starts_at = $starts_at->format("Y-m-d");
        $job->ends_at = $ends_at->format("Y-m-d");

        if ($request->hasFile("header_image"))
        {
            $job->header_image_url = Uploader::upload($request->file("header_image"), "images/jobs/header");
        }

        $job->save();

        $this->processJobResources($job, $request->resources);
        $this->processTeamRoles($job, $request->team_roles);

        return $job;
    }

    private function processJobResources(Job $job, $encodedResources)
    {
        // TODO: make this function smarter
        $job->resources()->delete();

        if (!is_null($encodedResources) and $encodedResources !== "" and $encodedResources !== "[]")
        {
            $resources = json_decode($encodedResources);
            if (is_array($resources) and count($resources))
            {
                foreach ($resources as $resource_id)
                {
                    $resource = JobResources::find($resource_id);
                    if ($resource)
                    {
                        $resource->job_id = $job->id;
                        $resource->save();
                    }
                }
            }
        }

        return $job;
    }

    private function processTeamRoles(Job $job, $encodedTeamRoles)
    {
        // TODO: make this function smarter; only delete those members that have actually been removed
        // so that existing team members don't lose their data. For MVP; delete that shiiiit.
        $job->teamRoles()->delete();

        if (!is_null($encodedTeamRoles) and $encodedTeamRoles !== "" and $encodedTeamRoles !== "[]")
        {
            $teamRoles = json_decode($encodedTeamRoles);
            if (is_array($teamRoles) and count($teamRoles))
            {
                foreach ($teamRoles as $teamRole)
                {
                    $skill_ids = [];
                    if (count($teamRole->skills))
                    {
                        foreach ($teamRole->skills as $skillName)
                        {
                            $skill = Skills::findOrCreateByName($skillName);
                            if ($skill) $skill_ids[] = $skill->id;
                        }
                    }

                    $tm = TeamRole::create([
                        "job_id" => $job->id,
                        "name" => $teamRole->name,
                        "description" => $teamRole->description
                    ]);
                    
                    if (count($skill_ids)) $tm->skills()->attach($skill_ids);
                }
            }
        }

        return $job;
    }
}