

<?php $__env->startSection('content'); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
                    <h2 class="text-2xl font-bold mb-6">Your Saved Medications</h2>

                    
                    <?php if(session('success')): ?>
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if(session('message')): ?>
                        <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
                            <?php echo e(session('message')); ?>

                        </div>
                    <?php endif; ?>

                    
                    <?php if($errors->any()): ?>
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($userDrugs->isEmpty()): ?>
                        <p class="text-gray-600">You haven't added any drugs yet.</p>
                    <?php else: ?>
                        <table class="w-full table-auto border border-gray-300 rounded shadow-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border px-4 py-2 text-left">RxCUI</th>
                                    <th class="border px-4 py-2 text-left">Name</th>
                                    <th class="border px-4 py-2 text-left">Base Names</th>
                                    <th class="border px-4 py-2 text-left">Dose Form</th>
                                    <th class="border px-4 py-2 text-left">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $userDrugs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border px-4 py-2"><?php echo e($drug['rxcui']); ?></td>
                                        <td class="border px-4 py-2"><?php echo e($drug['name']); ?></td>
                                        <td class="border px-4 py-2">
                                            <?php if(!empty($drug['baseNames']) && is_array($drug['baseNames'])): ?>
                                                <?php echo e(implode(', ', $drug['baseNames'])); ?>

                                            <?php else: ?>
                                                <em>N/A</em>
                                            <?php endif; ?>
                                        </td>
                                        <td class="border px-4 py-2">
                                            <?php if(!empty($drug['doseForms']) && is_array($drug['doseForms'])): ?>
                                                <?php echo e(implode(', ', $drug['doseForms'])); ?>

                                            <?php else: ?>
                                                <em>N/A</em>
                                            <?php endif; ?>
                                        </td>
                                        <td class="border px-4 py-2">
                                            <form action="<?php echo e(route('drugs.destroy', $drug['rxcui'])); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this drug?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button class="text-red-600 hover:underline">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <div class="mt-6">
                        <a href="<?php echo e(route('drugs.create')); ?>" class="text-blue-600 hover:underline font-medium">
                            ➕ Add another drug
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\LaravelDemo\rxnorm-demo\resources\views/drugs/index.blade.php ENDPATH**/ ?>