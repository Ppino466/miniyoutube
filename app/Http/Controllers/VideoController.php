<?php

namespace App\Http\Controllers;

use App\Models\Vsvideo;
use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Muestra lo registros
        $vs_videos = Vsvideo::where('status', '=', 1)->get();
        $videos = $this->cargarDT($vs_videos);
        return view('video.index')->with('videos', $videos);
    }

    public function cargarDT($consulta)
    {
        $videos = [];

        foreach ($consulta as $key => $value) {

            $ruta = "eliminar" . $value['id'];
            $eliminar = route('delete-video', $value['id']);

            $actualizar = route('videos.edit', $value['id']);
            $detalle = route('videos.show', $value['id']);
            $acciones = '
               
            <div class="btn-acciones">
                    <div class="btn-group">
                        <a href="' . $detalle . '" role="button" class="btn btn-outline-primary btn-sm" title="Reproducir">
                            <i class="bi bi-play-btn"></i>
                        </a>
                        <a href="' . $actualizar . '" role="button" class="btn btn-outline-success btn-sm" title="Actualizar">
                            <i class="bi bi-pencil"></i>
                        </a>
                         <a href="#' . $ruta . '" role="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#' . $ruta . '">
                            <i class="bi bi-trash"></i>
                        </a>

                    </div>
                </div>

                 <!-- Modal -->
            <div class="modal fade" id="' . $ruta . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">¿Seguro que deseas eliminar este video?</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-primary">
                        <small>
                            ' . $value['id'] . ', ' . $value['title'] . '                 </small>
                      </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

                      <a href="' . $eliminar . '" type="button" class="btn btn-danger">Eliminar</a>
                        </div>
                    </div>
                </div>
            </div>

            ';
            if ($value['image'] != '') {
                $miniatura = '<img src="./images/' . $value['image'] . '">';
            } else {
                $miniatura = '';
            }
            $videos[$key] = array(

                $value['id'],
                $value['title'],
                $value['description'],
                $miniatura,
                $value['video_path'],
                $value['name'],
                $value['email'],
                $acciones
            );
        }

        return $videos;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //Abre el formulario de captura de registros
        return view('video.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Guardar un nuevo registro 
        //Validación de formulario
        $validateData = $this->validate($request, [
            'title' => 'required|min:5',
            'description' => 'required',
            'video' => 'mimes:mp4'
        ]);
        $video = new Video();
        $user = Auth::user();
        $video->user_id = $user->id;
        $video->title = $request->input('title');
        $video->description = $request->input('description');
        //Subida de la miniatura
        $image = $request->file('image');
        if ($image) {
            $image_path = time() . $image->getClientOriginalName();
            \Storage::disk('images')->put($image_path, \File::get($image));
            $video->image = $image_path;
        }
        //Subida del video
        $video_file = $request->file('video');
        if ($video_file) {
            $video_path = time() . $video_file->getClientOriginalName();
            \Storage::disk('videos')->put($video_path, \File::get($video_file));
            $video->video_path = $video_path;
        }
        $video->save();
        return redirect()->route('videos.create')->with(array(
            'message' => 'El video se ha subido correctamente'
        ));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Muestra un registro
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        //Abre el formulario de edicion donde viene precargados los datos
        $user = \Auth::user();
        $video = Video::findOrFail($id);
        if ($user && $video->user_id == $user->id) {
            return view('video.edit', array(
                'video' => $video

            ));
        } else {
            redirect()->route('dashboard');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($video_id, Request $request)
    {
        //Guarda la informacion modificada capturada
        $validate = $this->validate($request, [
            'title' => 'required|min:5',
            'description' => 'required',
            'video' => 'mimes:mp4'
        ]);
        $user = \Auth::user();
        $video = Video::findOrFail($video_id);
        $video->user_id = $user->id;
        $video->title = $request->input('title');
        $video->description = $request->input('description');
        //Subida de la miniatura
        $image = $request->file('image');
        if ($image) {  //comprobamos que la imagen no sea nula
            $image_path = time() . $image->getClientOriginalName();
            \Storage::disk('images')->put($image_path, \File::get($image));
            $video->image = $image_path;
            //Subida del video
            $video_file = $request->file('video');
            if ($video_file) {
                $video_path = time() . $video_file->getClientOriginalName();
                \Storage::disk('videos')->put($video_path, \File::get($video_file));
                //Aquí se borraria el video antiguo
                $video->video_path = $video_path;
            }
            $video->update();
            return redirect()->route('videos.index')->with(array('message' => 'El video se ha actualizado Correctamente'));
        } //Fin de update

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Borra el registro

    }

    public function delete($video_id)
    {
        $video = Video::find($video_id);
        if ($video) {
            $video->status = 0;
            $video->update();
            return redirect()->route('videos.index')->with(array(
                'message' => "El video se ha eliminado correctamente"
            ));
        } else {
            return redirect()->route('videos.index')->with(array(
                'message' => "El video no existe"
            ));
        }
    }

    public function getVideo($filename){
        $file = \Storage::disk('videos')->get($filename);
        return new Response($file, 200);
     }
     
}
