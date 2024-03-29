<?php

namespace App\Http\Controllers\Webhooks;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Magyarjeti\MimeTypes\MimeTypeConverter;
use App\Models\Message;
use Illuminate\Support\Facades\Storage;
use Twilio;

class TwilioController extends Controller
{
    public function handleSMS(Request $request)
    {
        $body = $request->Body;
        $from = $request->From;
        $to = $request->To;
        $segments = $request->NumSegments;

        $message = Message::create([
            'body' => strip_tags($body),
            'segments' => $segments,
            'type' => (int) $request->NumMedia ? 'mms' : 'sms',
            'twilio_sid' => $request->MessageSid
        ]);

        $this->saveMedia($message, $request);

        return response('<Response></Response>')->header('Content-Type', 'text/xml');
    }

    protected function saveMedia(Message $message, Request $request)
    {
        if ($message->type !== 'mms') {
            return;
        }

        $media =

            $converter = new MimeTypeConverter;
        $numMedia = (int) $request->NumMedia;

        for ($i = 0; $i < $numMedia; $i++) {

            $mediaUrl = $request->input("MediaUrl$i");
            $MIMEType = $request->input("MediaContentType$i");
            $fileExtension = $converter->toExtension($MIMEType);
            $mediaSid = basename($mediaUrl);

            $media = $content = (new Client)->get($mediaUrl, [
                'auth' => [env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN')],
            ])->getBody();

            $filename = "$mediaSid.$fileExtension";
            $path = "messages/$message->twilio_sid/$filename";

            Storage::put($path, $media, 'public');

            $message->media()->create([
                'twilio_id' => $mediaSid,
                'content_type' => $MIMEType,
                'filename' => Storage::url($path)
            ]);
        }
    }
}
