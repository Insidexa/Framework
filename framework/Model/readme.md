use ActiveRecord

```
$post = new Post();
 
$data = $post
    ->select('all')
    ->where(['id' => 2])
    ->limit(1)
    ->get();
 
var_dump($data);
 
var_dump($post->find('all'));
 
$post->title = 'title';
$post->content = 'content';
$post->date = date('Y-m-d H:i:s');
$post->save();
 
echo $post->findById(2)->title;
echo $post->findById(2)->nickname;
 
$res = $post->where([
    'id' => 2,
])->update([
    'title' => 'new title1',
]);
 
var_dump($res);
 
$post->where([
    'id' => 2
])->delete();
```