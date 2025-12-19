# Plan Module « Blog / Actualités Santé » – LOBIKO

## 1. Inventaire de l’existant
- Migrations (2025_08_12_063601_create_contact_messages_table.php) contiennent déjà :
  - `article_categories` : id, name, slug unique, description, icon, order, is_active, timestamps, index slug.
  - `articles` : id, title, slug unique, excerpt, content (longText), featured_image, author_id (users FK), category_id (article_categories FK, nullable set null), is_published, is_featured, views_count, tags (json), meta_data (json), published_at, timestamps, index (is_published, published_at) + fulltext(title, excerpt, content).
  - `services`, `statistiques`, `faqs`, `partners`, `testimonials`, `contact_messages` (non blog mais proches front).
- Routes/Vues : pages placeholder pour blog (`/blog`, `/blog/{slug}`) dans routes/web.php. Pas de contrôleurs ni vues backend CRUD pour articles/categories/tags. Pas de tags table (tags sont en JSON dans articles). Pas de médias dédiés.
- Auth/RBAC : Spatie présent, policies déjà utilisées; rôles Admin/Super Admin utilisés; pas de permissions spécifiques blog.

## 2. Périmètre cible
- Articles, Catégories, Tags (vraie table), Médias (bibliothèque), option Commentaires (modération).
- Workflow : draft → review → published → archived; scheduling via published_at futur.
- SEO : slug unique, meta_title/meta_description, canonical_url.
- Stats : views_count déjà là; option post_views table si besoin.

## 3. Modèle de données à compléter/créer
- `articles` (existant) : ajouter colonnes `status` (draft/review/published/archived), `meta_title`, `meta_description`, `canonical_url`, `read_time`, soft deletes. Conserver slug unique.
- `article_categories` (existant) : éventuellement `parent_id` pour hiérarchie, soft deletes.
- `blog_tags` : id, name, slug unique, is_active, timestamps.
- Pivot `article_tag` : article_id, tag_id (unique pair), timestamps.
- Médias `media_files` : id (uuid), path, disk, mime, size, original_name, alt_text, caption, uploader_id (users), mediable morph (optionnel), timestamps, soft deletes.
- Commentaires (optionnel) `article_comments` : id, article_id FK, author_id FK (users), content, status (pending/approved/rejected), parent_id nullable, timestamps, soft deletes.

Indexes/FK :
- articles : index slug, status, published_at, author_id, category_id; fulltext conservé.
- tags : slug unique.
- pivots : unique(article_id, tag_id).
- medias : uploader_id, mediable_type/id si activé.
- commentaires : article_id, status, parent_id.

## 4. Models & relations
- `Article` : belongsTo Author (User), belongsTo Category, belongsToMany Tags, hasMany Comments, scopes published(), draft(), scheduled(), byCategory(), byTag(), byAuthor().
- `ArticleCategory` : hasMany Articles, optional parent/children.
- `BlogTag` : belongsToMany Articles.
- `MediaFile` : belongsTo uploader, morphTo mediable (optionnel).
- `ArticleComment` (option) : belongsTo Article, belongsTo Author, parent/children.

## 5. FormRequests
- `BlogPostStoreRequest` / `BlogPostUpdateRequest` : title, slug unique, content required, status in [draft,review,published,archived], published_at nullable date, category_id exists, tags array ids, meta_*, featured_image optional.
- `CategoryStoreRequest` / `CategoryUpdateRequest` : name, slug unique, is_active boolean, parent_id nullable.
- `TagStoreRequest` / `TagUpdateRequest` : name, slug unique, is_active boolean.
- `MediaUploadRequest` : file max size/type, alt_text/caption optional.

## 6. Services
- `SlugService` : génération slug unique (y compris mise à jour).
- `BlogPublishService` : transitions status (publish/unpublish/archive/schedule), remplit published_at, vérifie permissions.
- `MediaService` : upload sécurisé (disk configurable), données meta, suppression.
- `SeoService` (option) : fallback meta_title/description depuis content.

## 7. Controllers (backend admin)
- `BlogPostController` : index (filtres statut/auteur/catégorie/tag/date), create/store, edit/update, destroy (soft), publish, unpublish, archive, restore, uploadImage (option).
- `BlogCategoryController`, `BlogTagController` : CRUD.
- `MediaController` : index (bibliothèque), store (upload), destroy.
- `BlogCommentController` (option) : modération (approve/reject/delete).

## 8. Policies / Permissions (Spatie)
- Permissions : `blog.view`, `blog.create`, `blog.update`, `blog.delete`, `blog.publish`, `blog.moderate`, `blog.media.manage`.
- Policies par modèle (Article, ArticleCategory, BlogTag, MediaFile, ArticleComment).
- Menus et boutons protégés via `@canany(['blog.view', 'blog.create'])`.

## 9. Routes
- Prefix backend `/admin/blog` (middleware auth, éventuellement verified, permission).
- Nommage : `admin.blog.posts.*`, `admin.blog.categories.*`, `admin.blog.tags.*`, `admin.blog.media.*`, `admin.blog.comments.*`.
- Actions spécifiques : POST `/posts/{post}/publish`, `/unpublish`, `/archive`, `/restore`.

## 10. Vues Blade (backend)
- `admin/blog/posts/index/create/edit/show`: liste avec filtres (statut, auteur, catégorie, tag, date), actions Publish/Unpublish/Archive, pagination. Formulaire avec meta SEO, tags multi-select, image, scheduling (date).
- `admin/blog/categories/*`, `admin/blog/tags/*` CRUD simples.
- `admin/blog/media/index` : liste fichiers, upload modal, suppression.
- `admin/blog/comments/index` (si activé) : modération (approve/reject).
- UI : composants Lobiko pour tables, filtres, boutons, confirm modals.

## 11. Seeders / Factories
- Catégories santé (Cardiologie, Nutrition, Prévention…), tags (hypertension, diabète, bien-être, vaccination…).
- 30 articles démo (draft/published) avec tags et catégories.
- Medias de démo (images placeholder).

## 12. Tests à prévoir
- CRUD Articles/Catégories/Tags : create/update/delete avec 200, 403 si sans permission.
- Workflow publication : publish/unpublish/archive/restore, scheduling futur.
- Slug unique (création et mise à jour).
- Media upload : type/mime/size invalide => 422 ; valide => 201 et fichier stocké.
- Comment moderation (si activé) : approve/reject permissions.

## 13. Menus Backend
- Section “Blog / Actualités santé” (sidebar) avec : Articles, Catégories, Tags, Médias, Commentaires (si activé). Visibilité via @canany(blog.view/blog.create/...).

