<?php

namespace App\Http\Controllers\Api;

use App\Criteria\ReviewCriteria;
use App\Http\Requests\Api\CreateReviewAPIRequest;
use App\Http\Requests\Api\UpdateReviewAPIRequest;
use App\Models\Review;
use App\Repositories\Admin\ReviewRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ReviewController
 * @package App\Http\Controllers\Api
 */
class ReviewAPIController extends AppBaseController
{
    /** @var  ReviewRepository */
    private $reviewRepository;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepository = $reviewRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/reviews",
     *      summary="Get a listing of the Reviews.",
     *      tags={"Review"},
     *      description="Get all Reviews",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="limit",
     *          description="Change the Default Record Count. If not found, Returns All Records in DB.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *     @SWG\Parameter(
     *          name="offset",
     *          description="Change the Default Offset of the Query. If not found, 0 will be used.",
     *          type="integer",
     *          required=false,
     *          in="query"
     *      ),
     *      @SWG\Parameter(
     *          name="quotation_id",
     *          description="Qoutation Id.",
     *          type="integer",
     *          required=true,
     *          in="query"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Review")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->reviewRepository->pushCriteria(new RequestCriteria($request));
        $this->reviewRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->reviewRepository->pushCriteria(new ReviewCriteria($request));
        $reviews = $this->reviewRepository->all();

        return $this->sendResponse($reviews->toArray(), 'Reviews retrieved successfully');
    }

    /**
     * @param CreateReviewAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/reviews",
     *      summary="Store a newly created Review in storage",
     *      tags={"Review"},
     *      description="Store Review",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Review that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Review")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Review"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateReviewAPIRequest $request)
    {
        $input = $request->all();

        $checkReview = $this->reviewRepository->findWhere(['quotation_id'=>$input['quotation_id']]);
//        var_dump($checkReview);exit();

        if($checkReview->count() == 0){
            $reviews = $this->reviewRepository->create($input);
            return $this->sendResponse($reviews->toArray(), 'Review saved successfully');
        }

        return $this->sendErrorWithData('Review already Exist',200,$checkReview->toArray());
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/reviews/{id}",
     *      summary="Display the specified Review",
     *      tags={"Review"},
     *      description="Get Review",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="Authorization",
     *          description="User Auth Token{ Bearer ABC123 }",
     *          type="string",
     *          required=true,
     *          default="Bearer ABC123",
     *          in="header"
     *      ),
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Review",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Review"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Review $review */
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            return $this->sendErrorWithData('Review not found');
        }

        return $this->sendResponse($review->toArray(), 'Review retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateReviewAPIRequest $request
     * @return Response
     *
     * //@SWG\Put(
     *      path="/reviews/{id}",
     *      summary="Update the specified Review in storage",
     *      tags={"Review"},
     *      description="Update Review",
     *      produces={"application/json"},
     *      //@SWG\Parameter(
     *          name="id",
     *          description="id of Review",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      //@SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Review that should be updated",
     *          required=false,
     *          //@SWG\Schema(ref="#/definitions/Review")
     *      ),
     *      //@SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          //@SWG\Schema(
     *              type="object",
     *              //@SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              //@SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/Review"
     *              ),
     *              //@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateReviewAPIRequest $request)
    {
        $input = $request->all();

        /** @var Review $review */
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            return $this->sendErrorWithData('Review not found');
        }

        $review = $this->reviewRepository->update($input, $id);

        return $this->sendResponse($review->toArray(), 'Review updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * //@SWG\Delete(
     *      path="/reviews/{id}",
     *      summary="Remove the specified Review from storage",
     *      tags={"Review"},
     *      description="Delete Review",
     *      produces={"application/json"},
     *      //@SWG\Parameter(
     *          name="id",
     *          description="id of Review",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      //@SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          //@SWG\Schema(
     *              type="object",
     *              //@SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              //@SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              //@SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Review $review */
        $review = $this->reviewRepository->findWithoutFail($id);

        if (empty($review)) {
            return $this->sendErrorWithData('Review not found');
        }

        $review->delete();

        return $this->sendResponse($id, 'Review deleted successfully');
    }
}
