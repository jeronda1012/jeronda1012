<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyWebsiteRequest;
use App\Http\Requests\StoreWebsiteRequest;
use App\Http\Requests\UpdateWebsiteRequest;
use App\Models\DatabaseServer;
use App\Models\Office;
use App\Models\TechnologyUsed;
use App\Models\WebServer;
use App\Models\Website;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebsitesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('website_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $websites = Website::with(['office', 'hosting_server', 'database_server', 'platform'])->get();

        return view('admin.websites.index', compact('websites'));
    }

    public function create()
    {
        abort_if(Gate::denies('website_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $offices = Office::pluck('office_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $hosting_servers = WebServer::pluck('server_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $database_servers = DatabaseServer::pluck('server_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $platforms = TechnologyUsed::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        return view('admin.websites.create', compact('offices', 'hosting_servers', 'database_servers', 'platforms'));
    }

    public function store(StoreWebsiteRequest $request)
    {
        $website = Website::create($request->all());

        return redirect()->route('admin.websites.index');
    }

    public function edit(Website $website)
    {
        abort_if(Gate::denies('website_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $offices = Office::pluck('office_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $hosting_servers = WebServer::pluck('server_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $database_servers = DatabaseServer::pluck('server_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $platforms = TechnologyUsed::pluck('name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $website->load('office', 'hosting_server', 'database_server', 'platform');

        return view('admin.websites.edit', compact('offices', 'hosting_servers', 'database_servers', 'platforms', 'website'));
    }

    public function update(UpdateWebsiteRequest $request, Website $website)
    {
        $website->update($request->all());

        return redirect()->route('admin.websites.index');
    }

    public function show(Website $website)
    {
        abort_if(Gate::denies('website_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $website->load('office', 'hosting_server', 'database_server', 'platform');

        return view('admin.websites.show', compact('website'));
    }

    public function destroy(Website $website)
    {
        abort_if(Gate::denies('website_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $website->delete();

        return back();
    }

    public function massDestroy(MassDestroyWebsiteRequest $request)
    {
        Website::whereIn('id', request('ids'))->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
