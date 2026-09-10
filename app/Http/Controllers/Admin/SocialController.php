<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SocialController extends Controller
{
    private const SETTING_KEY = 'socials';

    /**
     * Display a listing of social links.
     */
    public function index()
    {
        $socials = $this->socials();

        return view('admin.socials.index', [
            'title' => __('admin.socials.title'),
            'socials' => $socials,
        ]);
    }

    /**
     * Show the form for creating a new social link.
     */
    public function create()
    {
        return view('admin.socials.create', ['title' => __('admin.socials.add')]);
    }

    /**
     * Store a newly created social link.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('socials', 'public');
        }

        $socials = $this->socials();
        $socials[] = ['id' => uniqid()] + $data;
        $this->save($socials);

        return redirect()->route('admin.socials.index')
            ->with('success', __('admin.socials.created'));
    }

    /**
     * Show the form for editing a social link.
     */
    public function edit(string $social)
    {
        $socialLink = $this->findSocial($social);
        abort_unless($socialLink, 404);

        return view('admin.socials.edit', [
            'title' => __('admin.socials.edit'),
            'social' => $socialLink,
        ]);
    }

    /**
     * Update the specified social link.
     */
    public function update(Request $request, string $social)
    {
        $socialLink = $this->findSocial($social);
        abort_unless($socialLink, 404);

        $data = $this->validated($request);

        if ($request->hasFile('icon')) {
            $data['icon'] = $request->file('icon')->store('socials', 'public');
        }

        $socials = $this->socials();
        foreach ($socials as $key => $item) {
            if (($item['id'] ?? null) === $socialLink['id']) {
                $socials[$key] = $data + ['id' => $socialLink['id']];
                break;
            }
        }
        $this->save($socials);

        return redirect()->route('admin.socials.index')
            ->with('success', __('admin.socials.updated'));
    }

    /**
     * Remove the specified social link.
     */
    public function destroy(string $social)
    {
        $socials = $this->socials();
        $socials = array_values(array_filter(
            $socials,
            fn (array $item): bool => ($item['id'] ?? null) !== $social
        ));
        $this->save($socials);

        return redirect()->route('admin.socials.index')
            ->with('success', __('admin.socials.deleted'));
    }

    /**
     * Validate and prepare a social link payload.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.en' => ['required', 'string', 'max:255'],
            'name.ar' => ['required', 'string', 'max:255'],
            'link' => ['required', 'url', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /**
     * List the social links stored in settings.
     *
     * @return array<int, array<string, mixed>>
     */
    private function socials(): array
    {
        $socials = Setting::query()->where('key', self::SETTING_KEY)->value('value') ?? [];

        return array_map(
            fn (array $item): array => ['id' => $item['id'] ?? uniqid()] + $item,
            $socials
        );
    }

    /**
     * Find a social link by its id.
     *
     * @return array<string, mixed>|null
     */
    private function findSocial(string $id): ?array
    {
        foreach ($this->socials() as $item) {
            if (($item['id'] ?? null) === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Persist the social links setting.
     *
     * @param  array<int, array<string, mixed>>  $socials
     */
    private function save(array $socials): void
    {
        Setting::query()->updateOrCreate(
            ['key' => self::SETTING_KEY],
            ['value' => $socials]
        );
    }
}
