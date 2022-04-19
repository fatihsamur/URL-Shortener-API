<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;


class ShortLinkController extends Controller
{
    //
    public function store(Request $request)
    {
        // validate input
        $request->validate([
            'link' => 'required|url',
        ]);

        // check if link already exists
        $shortLink = ShortLink::where('link', $request->link)->first();
        if ($shortLink) {
            return response()->json([
                'message' => 'Link already exists',
                'code' => $shortLink->code,
            ], 200);
        }

        // store url and short url and user
        $input['link'] = $request->link;
        $input['code'] = $this->generateShortLink();
        $input['user_id'] = $request->user()->id;

        // save to database
        $shortlink = ShortLink::create($input);

        // return short url
        return response()->json([
            'code' => $shortlink->code,
            'link' => $shortlink->link,
            'user' => $shortlink->user->name,
            'message' => 'Short link generated successfully.'
        ]);
    }

    // generate short url
    public function generateShortLink()
    {
        $shortLink = Str::random(6);
        $shortLinkExists = ShortLink::where('code', $shortLink)->exists();

        if ($shortLinkExists) {
            return $this->generateShortLink();
        }

        return $shortLink;
    }

    public function shortenedLink($code)
    {
        $find = ShortLink::where('code', $code)->first();

        return response()->json([
            'code' => $find->code,
            'originalLink' => $find->link,

        ]);
    }

    // get all short links of a user
    public function getAllShortLinks(Request $request)
    {
        $user = $request->user();
        $shortLinks = $user->shortLinks;

        return response()->json([
            'shortLinks' => $shortLinks,
        ]);
    }
}
