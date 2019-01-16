<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $esAdmin = $options['esAdmin'];
        $esSuper = $options['esSuper'];
        $disabled = $options['disab'];
        $auxdisabled = $options['disab'];
        if ($esAdmin)
            $auxdisabled = false;
        if ($esSuper) {
            $builder
                ->add('institucion', null, array('label' => 'Institución', 'required' => false, 'disabled' => $disabled, 'attr' => array('class' => 'form-control input-medium')))
                ->add('idrol', null, array('label'=>'Permisos', 'required' => true,'disabled' => $disabled, 'attr' => array('class' => 'form-control input-medium')));
        }else{
            $builder->add('idrol',null,array(
                'required'=>true,
                'label'=>'Permisos',
                'disabled' => $disabled,
                'auto_initialize'=>false,
                'class'         =>'App:Rol',
                'query_builder'=>function(EntityRepository $repository){
                    $qb=$repository->createQueryBuilder('r');
                    $qb->where('r.nombre<> :super')->setParameter('super','ROLE_SUPERADMIN');
                    return $qb;
                }
            ));
        }


        $builder
            ->add('usuario', TextType::class, array('attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large')))

            ->add('activo',null,['disabled' => $disabled,])
            ->add('nombre', TextType::class, array('attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('apellidos', TextType::class, array('attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large','pattern' => '^([A-Za-záéíóúñ]{2,}((\s[A-Za-záéíóúñ]{2,})*))*$')))
            ->add('correo', EmailType::class, array('label'=>'Correo electrónico','attr' => array('autocomplete' => 'off', 'class' => 'form-control input-large','pattern'=>'^[a-z0-9!#$%&\'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$')))

        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $obj) {
            $form = $obj->getForm();
            $data = $obj->getData();
            $required = false;
            $constraint=array();
            if (null == $data->getId()){
                $required = true;
                $constraint[]=new Assert\NotBlank();
            }

            $form->add('password', RepeatedType::class, array('required' => $required,
                'type' => PasswordType::class,
                'constraints' => $constraint,
                'invalid_message' => 'Las dos contraseñas deben coincidir',
                'first_options' => array('label' => 'Contraseña'
                , 'attr' => array('class' => 'form-control input-medium')),
                'second_options' => array('label' => 'Confirmar contraseña', 'attr' => array('class' => 'form-control input-medium'))
            ));
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'disab' => false,
        ]);
        $resolver->setRequired(['esAdmin','esSuper']);
    }
}
