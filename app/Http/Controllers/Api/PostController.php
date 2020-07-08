<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Post;
use App\PostImage;
use App\Category;
use App\Gallery;
use App\Galleryimage;
use JWTAuth;
use Illuminate\Support\Str;
use Validator;

class PostController extends Controller
{
    public function listcategories() {
        $categories = Category::all();
        return response()->json([
                'success' => true,
                'data' => $categories
            ]);
    }
    
    public function addcategory(Request $request) {
        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name, '-');
        $category->save();
        
        return response()->json([
                'success' => true,
                'data' => $category
            ]);
    }
    
    public function listpost($id) {
        $posts = Post::with("user")->with("category")->orderBy('created_at', 'desc')->jsonPaginate($id);
        if($posts) {
            return response()->json([
                'success' => true,
                'data' => $posts
            ]);    
        }
        
        return response()->json([
                'success' => false
            ]);
        
    }
    
    public function getgalleries() {
        $galleries = Gallery::with("galleryimages")->orderBy('created_at', 'desc')->jsonPaginate(20);
        
        if($galleries) {
            return response()->json([
                    'success' => true,
                    'data' => $galleries
                ]);
                
            return response()->json([
                    'success' => false,
                    'error' => 'unable to fetch galleries'
                ]);
        }
        
    }
    
    public function catPosts() {
        $posts = Post::where('id', $request->id)->category()->orderBy('created_at', 'desc')->jsonPaginate();
        if($posts) {
            return response()->json([
                'success' => true,
                'data' => $posts
            ]);   
        }
        return response()->json([
                'success' => false
            ]);
    }
    
    public function addPost(Request $request) {
        $user = JWTAuth::parseToken()->authenticate();
        $cat = Category::find($request->category);
        $post = new Post();
        $post->title = $request->title;
        $post->body = $request->body;
        $post->featured = $this->catch_that_image($request->body);
        // $post->category_id = $request->category;
        $post->slug = Str::slug($request->title, '-');
        $post->user()->associate($user);
        $post->category()->associate($cat);

        if($post->save()) {
            return response()->json([
                'success' => true,
                'data' => $post
            ]);    
        }
        
        return response()->json([
                'success' => false
            ]);
        
    }
    
    public function savepost_image(Request $request) {
    //     request()->validate([
    //       'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //   ]);
      $validator = Validator::make($request->all(), [
            'image' => 'required|image|size:2048',
        ]);
        if($validator->fails()) {
            return response()->json([
                'success'=> false,
                'error'=> $validator->messages()
            ]);
        }
      if ($request->hasFile('image')) {
            $imagePath = $request->file('image');
            // $imageName = time() . '.' . $imagePath ->getClientOriginalExtension();
            $imageName = time() . '_' . $imagePath->getClientOriginalName();
            // $imagePath ->move('fv-contents', $imageName );
        
          if($request->image->storeAs('public/fv-contents', $imageName)) {
              return response()->json([
                  'success' => true,
                  'data' => $imageName
                  ]);
          }
          
          
      } else {
          return response()->json([
                'success' => false,
                'data' => 'Image not uploaded'
              ]);
      }

    }
    
    public function singlepost($id) {
        $post = Post::find($id)->with("category")->with("user")->first();
        if($post) {
            return response()->json([
                'success' => true,
                'data' => $post
                ]);
        } else {
            return response()->json([
                    'success' => false,
                    'error' => 'Post not found'
                ]);
        }
    }
    
    public function updatepost(Request $request) {
        $post = Post::find($request->post_id)->first();
        if($post) {
            $post->title = $request->title;
            $post->body = $request->body;
            if($post->save()) {
                return response()->json([
                        'success' => true,
                        'data' => 'Post Updated Successfully'
                    ]);
            } else {
                return response()->json([
                        'success' => false,
                        'data' => 'An error occured while trying to update post'
                    ]);
            }
            
        } else {
            return response()->json([
                        'success' => false,
                        'data' => 'Post not found'
                    ], 404);
        }
    }
    
    public function catch_that_image($post) {
        $first_img = '';
        $output = preg_match_all('/<img.+?src=[\'"]([^\'"]+)[\'"].*?>/i', $post, $matches);
        $first_img = $matches[1][0];

        if(empty($first_img)) {
            $first_img = "https://fvisng.com/assets/images/no-image.jpg";
        }
        return $first_img;
    }
    
    public function getsinglepost($slug) {
        $get = Post::where('slug', $slug)->with("category")->with("user")->first();
        
        if($get) {
            return response()->json([
                    'success' => true,
                    'data' => $get
                ]);
        } else {
            return response()->json([
                    'success' => false
                ]);
        }
    }
    
    public function getsinglegallery($slug) {
        $gallery = Gallery::where('slug', $slug)->with('user')->with('galleryimages')->first();
        if($gallery) {
            foreach ($gallery->galleryimages as $check) {
                $data[] = [
                    'imageUrl' => $check->gallery_url,
                    'thumbUrl' => $check->gallery_url,
                    'caption' => $check->id
                    ];
            }
            return response([
                    'success' => true,
                    'title' => $gallery->title,
                    'author' => $gallery->user,
                    'date' => $gallery->created_at,
                    'data' => $data
                ]);            
        } else {
            return response()->json([
                    'success' => false
                ]);
        }
    } 
}
