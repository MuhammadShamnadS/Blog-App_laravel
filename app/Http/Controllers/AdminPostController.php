<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminPostEditorAssignRequest;
use App\Http\Requests\AdminPostFeatureRequest;
use App\Http\Requests\AdminPostScheduleRequest;
use App\Http\Services\AdminPostService;
use App\Models\Post;


class AdminPostController extends Controller
{
    protected $adminPost;

    public function __construct(AdminPostService $adminPost)
    {
        $this->adminPost = $adminPost;
    }

    // get posts that authors submitted
    public function submittedPosts()
    {
        return $this->adminPost->viewSubmittedPost();
    }

    // assign editor to a submitted or resubmitted post
    public function assignEditor(AdminPostEditorAssignRequest $request, $postId)
    {
        return $this->adminPost->handleAssignEditor($postId, $request->validated());
    }

    // get editors for a given category
    public function editors($categoryId)
    {
        return $this->adminPost->getEditors($categoryId);
    }

    //  get editor aproved posts by admin
    public function getEditorApprovedPosts()
    {
        return $this->adminPost->listPendingPost();
    }

    // schedule editor approved post for publish by admin
    public function schedulePost($id, AdminPostScheduleRequest $request)
    {
        return $this->adminPost->handleSchedule($id, $request->validated());
    }

    //  show single post by admin
    public function show($id)
    {
        return $this->adminPost->showSinglePost($id);
    }

    //  list published post by admin
    public function viewPublishedPost()
    {
        return $this->adminPost->listPublishedPost();
    }

    //  list archieved post by admin
    public function viewArchievedPost()
    {
        return $this->adminPost->listArchievedPost();
    }

    //  list featured post by admin
    public function viewFeaturedPost()
    {
        return $this->adminPost->listFeaturedPosts();
    }

    //  list scheduled post by admin
    public function viewScheduledPost()
    {
        return $this->adminPost->listScheduledPost();
    }

    //  feature or unfeature a post by admin
    public function toggleFeatured($id, AdminPostFeatureRequest $request)
    {
        return $this->adminPost->featurePost($id, $request->validated());
    }

    //  publish a post by admin
    public function publish($id)
    {
        return $this->adminPost->handlePublish($id);
    }

    //  delete a post by admin
    public function destroy($id)
    {
        return $this->adminPost->handleDelete($id);
    }

    // archive a post by admin
    public function archive($id)
    {
        return $this->adminPost->handleArchieve($id);
    }

    // unarchive a post by admin
    public function unarchive($id)
    {
        return $this->adminPost->handleUnarchieve($id);
    }

    //  list category by admin
    public function category()
    {
        return $this->adminPost->fetchCategories();
    }
}
